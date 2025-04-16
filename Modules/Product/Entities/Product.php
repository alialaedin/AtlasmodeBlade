<?php

namespace Modules\Product\Entities;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Media;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Classes\DontAppend;
use Modules\Core\Transformers\MediaResource;
use Modules\Customer\Entities\Customer;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Entities\Admin;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Helpers\Str;
use Modules\Core\Traits\HasDefaultFields;
use Modules\Core\Traits\HasMorphAuthors;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Flash\Entities\Flash;
use Modules\Order\Entities\OrderItem;
use Modules\Product\Services\ProductService;
use Modules\ProductComment\Entities\ProductComment;
use Modules\SizeChart\Entities\SizeChart;
use Modules\Specification\Entities\Specification;
use Modules\Specification\Entities\SpecificationValue;
use Modules\Unit\Entities\Unit;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Tags\HasTags;
use Modules\Product\Entities\Variety;

class Product extends BaseModel implements HasMedia, Viewable
{
	use InteractsWithMedia, InteractsWithViews, HasMorphAuthors, HasTags, HasDefaultFields, LogsActivity;

	protected $defaults = [
		'chargeable' => 0
	];
	// protected $appends = [
	// 	'images',
	// 	'total_quantity',
	// 	'price',
	// 	'rate',
	// 	'major_discount_amount',
	// 	'major_image',
	// 	'major_gifts',
	// 	'major_final_price',
	// 	'views_count'
	// ];
	protected $hidden = ['media'];
	protected $with = ['activeFlash'];
	protected $fillable = [
		'title',
		'short_description',
		'description',
		'unit_price',
		'purchase_price',
		'discount_type',
		'discount_until',
		'discount',
		'SKU',
		'barcode',
		'brand_id',
		'unit_id',
		'meta_description',
		'meta_title',
		'low_stock_quantity_warning',
		'show_quantity',
		'chargeable',
		'status',
		'approved_at',
		'published_at',
		'threshold_quantity',
		'threshold_date',
		'slug',
		'image_alt'
	];

	const STATUS_DRAFT         = "draft";  # چرک نویس -> غیرقابل فروش -> غیرقابل نمایش -> در v2
	const STATUS_SOON          = "soon";  # به زودی -> غیرقابل فروش -> قابل نمایش
	const STATUS_AVAILABLE     = "available";  # موجود -> قابل فروش -> قابل نمایش
	const STATUS_OUT_OF_STOCK  = "out_of_stock"; # ناموجود -> غیرقابل فروش ->  قابل نمایش
	const STATUS_AVAILABLE_OFFLINE   = "available_offline"; #  موجود -> قابل فروش ->  قابل نمایش نمیباشد -> فروش فقط توسط ادمین
	const STATUS_INIT_QUANTITY = 'init_quantity'; // موجودی اولیه -> فقط ثبت میشه نه میشه تو فروشگاه حضوری فروخت نه میشه بیرون سایت فروخت

	const DISCOUNT_TYPE_PERCENTAGE = "percentage";
	const DISCOUNT_TYPE_FLAT       = "flat";

	const ACCEPTED_IMAGE_MIMES = 'gif|png|jpg|jpeg|svg|webp';
	const DATE_FORMAT = 'Y-m-d H:i:s';

	public function sluggable(): array
	{
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		$withSetting = app(CoreSettings::class)->get('product.with');
		$gift = app(CoreSettings::class)->get('product.gift.active');
		if (!empty($withSetting)) {
			$this->with = array_unique(array_merge($this->with, $withSetting));
		}
		if ($gift) {
			if (auth()->user() instanceof Admin) {
				$this->with = array_merge($this->with, ['activeGifts', 'gifts']);
			} else {
				$this->with = array_merge($this->with, ['activeGifts']);
			}
		}
	}

	protected static function booted()
	{
		static::creating(function ($product) {
			$user = auth()->user();
			ProductService::deleteCache();
			if ($user instanceof Admin && ($user->can('approved_product'))) {
				$product->approved_at = Carbon::now()->toDateTimeString();
			}
			if (is_null($product->published_at)) {
				$product->published_at = now();
			}
		});
		static::updating(function () {
			ProductService::deleteCache();
		});
		static::deleting(function (Product $product) {
			if ($product->orderItems()->exists()) {
				throw Helpers::makeValidationException('به علت وجود سفارش برای این محصول امکان حذف آن وجود ندارد');
			}
			if ($rec = $product->recommendations()->first()) {
				$name = __('core::groups.' . $rec->group);
				throw Helpers::makeValidationException("این محصول در لیست محصولات پیشهادی ($name) انتخاب شده است ");
			}
			ProductService::deleteCache();
		});
	}

	public function getActivitylogOptions(): LogOptions
	{
		$admin = Auth::user() ?? Admin::query()->first();
		$name = !is_null($admin->name) ? $admin->name : $admin->username;
		return LogOptions::defaults()
			->useLogName('Product')->logAll()->logOnlyDirty()
			->setDescriptionForEvent(function ($eventName) use ($name) {
				$eventName = Helpers::setEventNameForLog($eventName);
				return "محصول {$this->title} توسط ادمین {$name} {$eventName} شد";
			});
	}

	//=================== statics ===================\\
	public static function getAvailableDiscountTypes(): array
	{
		return [static::DISCOUNT_TYPE_FLAT, static::DISCOUNT_TYPE_PERCENTAGE];
	}

	public static function makeHiddenForFront(Product $product, $other = [])
	{
		$product->makeHidden([
			'creatorable_id',
			'creatorable_type',
			'approved_at',
			'published_at',
			'unit_price',
			'purchase_price',
			'updaterable_id',
			'updaterable_type',
			...$other
		]);
	}

	public static function checkStatusChanges($requestProduct)
	{
		$varieties = $requestProduct['varieties'] ?? null;
		if (!empty($varieties)) {
			$hasAnyVariety = false;
			foreach ($varieties as $variety) {
				if (($variety['quantity'] != 0)) {
					$hasAnyVariety = true;
				}
			}
			if (!$hasAnyVariety && ($requestProduct['status'] == static::STATUS_AVAILABLE)) {
				throw new \Exception('زمانی که موجودی محصول صفر است نمیتواند وضعیت آن موجود باشد');
			}
		} else {
			if ($requestProduct['quantity'] == 0 && $requestProduct['status'] == static::STATUS_AVAILABLE) {
				throw new \Exception('زمانی که موجودی محصول صفر است نمیتواند وضعیت آن موجود باشد');
			}
		}
	}

	public static function getStatusCounts()
	{
		$counts = [];
		foreach (static::getAvailableStatuses() as $status) {
			$counts[$status] = static::query()->where('status', $status)->count();
		}

		return $counts;
	}

	public static function getAvailableStatuses(): array
	{
		return [
			static::STATUS_AVAILABLE,
			static::STATUS_OUT_OF_STOCK,
			static::STATUS_SOON,
			static::STATUS_DRAFT,
			static::STATUS_AVAILABLE_OFFLINE,
			static::STATUS_INIT_QUANTITY
		];
	}

	public static function getActiveVarietiyIds($productId)
	{
		return Variety::query()
			->select(['id', 'product_id', 'deleted_at'])
			->where('product_id', $productId)
			->withoutGlobalScopes()
			->whereNull('deleted_at')
			->pluck('id')
			->toArray();
	}

	public static function generateBarcode()
	{
		$lastProductBarcode = DB::table('products')->latest('barcode')->first()->barcode;
		$barcode = $lastProductBarcode + 1;
		while (Product::where('barcode', $barcode)->exists()) $barcode++;
		return $barcode;
	}

	public static function getRelatedProducts(self $product, int $take = 8)
	{
		$categories = $product->categories;
		$availableCategoryIds = count($categories->whereNotNull('parent_id')->pluck('id')->toArray()) > 0
			? $categories->whereNotNull('parent_id')->pluck('id')->toArray()
			: $categories->pluck('id')->toArray();

		return self::query()
			->select(['id', 'status', 'title', 'slug', 'image_alt', 'approved_at', 'published_at'])
			->available()
			->active()
			->whereKeyNot($product->id)
			->whereHas('categories', fn($q) => $q->whereIn('id', $availableCategoryIds))
			->with(['media', 'varieties' => fn($q) => $q->select(['id', 'product_id', 'price', 'discount', 'discount_until', 'discount_type'])])
			->latest('id')
			->take($take)
			->get()
			->each(fn($p) => $p->append(['main_image', 'final_price']));
	}

	public static function getAvailableStatusesWithLabel()
	{
		foreach (self::getAvailableStatuses() as $s) {
			$statuses[] = [
				'name' => $s,
				'label' => config('product.prdocutStatusLabels.' . $s)
			];
		}
		return $statuses;
	}

	public static function getAvailableDiscountTypesWithLabel()
	{
		foreach (self::getAvailableDiscountTypes() as $d) {
			$dTypes[] = [
				'name' => $d,
				'label' => config('product.productDiscountTypes.' . $d)
			];
		}
		return $dTypes;
	}

	//=================== stitics ===================\\


	//=================== scopes ===================\\
	public function scopeAvailable($query, $force = false)
	{
		$query->where('status', self::STATUS_AVAILABLE)
			->whereNotNull('approved_at');

		$customer = Auth::guard('customer')->user();
		if ($force || !($customer instanceof Customer) || !($customer->canSeeUnpublishedProducts())) {
			$query->where('published_at', "<=", Carbon::now());
		}
	}

	public function scopeActive($query, $operatorStatus = '!=', $status = self::STATUS_DRAFT)
	{
		//$status != init
		$query->whereNotIn('status', [self::STATUS_DRAFT, self::STATUS_INIT_QUANTITY])
			->whereNotNull('approved_at');

		$customer = Auth::guard('customer')->user();
		if (!($customer instanceof Customer) || !($customer->canSeeUnpublishedProducts())) {
			$query->where('published_at', "<=", Carbon::now());
		}
	}

	public function scopeHasCategory($query, $id)
	{
		$query->whereHas('categories', function ($q) use ($id) {
			$q->where('categories.id', $id);
		});
	}

	public function scopeFilters($query)
	{
		return $query
			->when(request('category_id'), function ($productQuery) {
				$productQuery->whereHas('categories', function ($categoriesQuery) {
					$categoriesQuery->where('id', request('category_id'))->orWhere('parent_id', request('category_id'));
				});
			})
			->when(in_array(request('vip'), ["1", "0"]), fn($q) => $q->where('published_at', '>', now()))
			->when(request('id'), fn($q) => $q->where('id', request('id')))
			->when(request('title'), fn($q) => $q->where('title', 'LIKE', "%" . request('title') . "%"))
			->when(request('start_date'), fn($q) => $q->whereDate('created_at', '>=', request('start_date')))
			->when(request('end_date'), fn($q) => $q->whereDate('created_at', '<=', request('end_date')))
			->when(request('status') && request('status') !== 'all', fn($q) => $q->where('status', request('status')))
			->when(in_array(request('is_approved'), ["1", "0"]), function ($q) {
				request('is_approved') == '1' ? $q->whereNotNull('approved_at') : $q->whereNull('approved_at');
			})
			->when(request('available'), fn($q) => $q->available())
			->when(request('attribute_value_id'), function ($productQuery) {
				$productQuery->whereHas('varieties', function ($varietyQuery) {
					$varietyQuery->whereHas('attributes', function ($attributeQuery) {
						$attributeValueIdsArr = json_decode(request()->attribute_value_id, true);
						$attributeQuery->whereIn('attribute_variety.attribute_value_id', $attributeValueIdsArr);
					});
				});
			});
	}

	//=================== scopes ===================\\

	public function addImages($images): void
	{
		if (empty($images)) return;
		Media::addMedia($images, $this, 'images');
	}

	public function updateImages($images): void
	{
		$updatedImages = Media::updateMedia($images, $this, 'images');
		$mediaToDelete = $this->media()->where('collection_name', 'images')->whereNotIn('id', $updatedImages)->get();
		foreach ($mediaToDelete as $media) {
			$media->delete();
		}
		$this->load('media');
	}

	//=================== getters ===================\\
	public function getImagesAttribute()
	{
		if (!$this->relationLoaded('media')) {
			return new DontAppend('Product getImagesAttribute');
		}
		$media = $this->getMedia('images');

		return MediaResource::collection($media);
	}

	public function getViewsCountAttribute()
	{
		return views($this)->count();
	}

	public function getTotalQuantityAttribute()
	{
		if (!$this->relationLoaded('varieties')) {
			$this->makeHidden('total_quantity');
			return new DontAppend('getTotalQuantityAttribute 1');
		}
		$balance = 0;
		$varieties = $this->varieties;
		foreach ($varieties as $variety) {
			if (!$variety->relationLoaded('store')) {

				return new DontAppend('getTotalQuantityAttribute 2');
			}
			$balance += $variety->store->balance ?? 0;
		}
		return $balance;
	}

	public function getTotalSalesAttribute()
	{
		if (!$this->relationLoaded('varietyOnlyIdsRelationship')) {
			return new DontAppend('Product getTotalSalesAttribute');
		}
		$varietyIds = $this->varietyOnlyIdsRelationship->pluck('id');
		return OrderItem::query()->whereIn('variety_id', $varietyIds)
			->whereHas('variety', function ($q) {
				$q->where('product_id', $this->id);
			})->sum('quantity');

		// return $this->orderItems->where('status', 1)->sum('quantity');
	}

	public function getMostDiscountAttribute()
	{
		if (!$this->relationLoaded('varietyOnlyDiscountsRelationship')) {
			return new DontAppend('Product getMostDiscountsAttribute');
		}
		$discount = 0;
		$varieties = $this->varietyOnlyDiscountsRelationship;
		foreach ($varieties as $variety) {
			if ($variety->final_price['discount_price'] > $discount) {
				$discount = $variety->final_price['discount_price'];
			}
		}
		return $discount;
	}

	public function getTotalFavoriteAttribute()
	{
		if (!$this->relationLoaded('favorite')) {
			return new DontAppend('favorite');
		}
		return $this->favorite->count();
	}

	public function getPriceAttribute()
	{
		if (!$this->relationLoaded('varieties')) {
			return new DontAppend('getPriceAttribute');
		}
		$finalPrice = PHP_INT_MAX;
		$secondFinalPrice = PHP_INT_MAX;
		foreach ($this->varieties as $variety) {
			if (
				$variety->quantity != 0 &&
				$variety->final_price['amount'] < $finalPrice
			) {
				$finalPrice = $variety->final_price['amount'];
			}
			if ($variety->final_price['amount'] < $secondFinalPrice) {
				$secondFinalPrice = $variety->final_price['amount'];
			}
		}

		return $finalPrice == PHP_INT_MAX ? $secondFinalPrice : $finalPrice;
	}

	public function getSlugAttribute()
	{
		if (
			isset($this->attributes['slug'])
			&& !empty($this->attributes['slug'])
		) {
			return $this->attributes['slug'];
		}
		if (!$this->title) {
			return;
		}
		return Str::slug($this->title);
	}

	public function getMainImageAttribute()
	{
		$media = $this->getFirstMedia('images');
		if ($media) {
			return new MediaResource($media);
		}
		$varieties = $this->varieties;
		foreach ($varieties as $variety) {
			$media = $variety->main_image;
			if ($media) {
				return new MediaResource($media);
			}
		}

		return null;
	}

	public function getMajorDiscountAmountAttribute()
	{
		if (!$this->relationLoaded('varieties')) {
			return new DontAppend('getMajorDiscountAmount');
		}
		$finalPrice = PHP_INT_MAX;
		$discount = 0;
		foreach ($this->varieties as $variety) {
			if (
				$variety->quantity != 0 &&
				$variety->final_price['amount'] < $finalPrice
			) {
				$finalPrice = $variety->final_price['amount'];
				$discount = $variety->final_price['discount_price'];
			}
		}

		return $discount;
	}

	public function getFinalPriceAttribute()
	{
		if (!$this->relationLoaded('varieties')) {
			return new DontAppend('getMajorDiscountPercentageAttribute');
		}
		$finalPrice = PHP_INT_MAX;
		$chosenVariety = null;
		foreach ($this->varieties as $variety) {
			if (
				$variety->store->balance != 0 &&
				$variety->final_price['amount'] < $finalPrice
			) {
				$chosenVariety = $variety;
			}
		}

		return $chosenVariety ? $chosenVariety->final_price : [];
	}

	public function getMajorImageAttribute()
	{
		if (!$this->relationLoaded('varieties')) {
			return new DontAppend('getMajorImageAttribute');
		}
		$firstMedia = $this->getFirstMedia('images');

		if ($firstMedia) {
			return new MediaResource($firstMedia);
		}

		$varieties = $this->varieties;
		foreach ($varieties as $variety) {
			if ($variety->quantity != 0) {
				$media = $variety->main_image;
				if ($media) {
					return new MediaResource($media);
				}
			}
		}

		return $this->getMainImageAttribute();
	}

	public function getMajorGiftsAttribute()
	{
		if (!$this->relationLoaded('varieties.activeGifts')) {
			return new DontAppend('getMajorGiftsAttribute');
		}
		$majorGifts = collect();
		$varieties = $this->varieties;
		foreach ($this->activeGifts as $activeGift) {
			$majorGifts->push($activeGift);
		}
		foreach ($varieties as $variety) {
			$gifts = $variety->activeGifts;
			foreach ($gifts as $gift) {
				$majorGifts->push($gift);
			}
		}

		return $majorGifts->unique();
	}

	public function getRateAttribute(): string
	{
		return Cache::remember('product-comment-' . $this->id, 240, function () {
			$avg = $this->productComments()
				->where('status', ProductComment::STATUS_APPROVED)
				->whereRaw('id IN (SELECT MAX(id) FROM product_comments GROUP BY creator_id)')
				->selectRaw('AVG(rate) AS avg')->first()->avg;
			return number_format($avg, 1);
		});
	}

	public function getVideoCoverAttribute()
	{
		if (!$this->relationLoaded('media')) {
			return new DontAppend('Product getVideoCoverAttribute');
		}
		$media = $this->getMedia('video_cover')->first();

		return new MediaResource($media);
	}

	public function getVideoAttribute()
	{
		if (!$this->relationLoaded('media')) {
			return new DontAppend('Product getVideoAttribute');
		}
		$media = $this->getMedia('video')->first();

		return new MediaResource($media);
	}

	public function getIsVipAttribute()
	{
		return $this->published_at > now();
	}

	public function getSettedProductsAttribute()
	{
		// so fucking important!!
		$product = clone $this;
		return Helpers::removeVarieties($product->setAppends([])
			->load([
				'sets.products' => fn($q) => $q->withOnly(['media', 'varieties' => fn($v) => $v->withOnly([])])->select(['products.id', 'products.title', 'products.slug'])
			])
			->sets->map(fn($set) => $set->products->each->setAppends(['major_image', 'video', 'video_cover']))
			->flatten(1)
			->filter(fn($prod) => (($prod->id !== $product->id) && $prod->majorImage))->values()->toArray());
		//
	}

	//=================== getters ===================\\



	//=================== setters ===================\\
	public function setStatusAttribute($status)
	{
		if (!in_array($status, static::getAvailableStatuses())) {
			throw Helpers::makeValidationException('وضعیت انتخاب شده نامعتبر است');
		}

		$this->attributes['status'] = $status;
	}

	public function setBarcodeAttribute($value)
	{
		$this->attributes['barcode'] = Helpers::convertFaNumbersToEn($value);
	}

	public function setSkuAttribute($value)
	{
		$this->attributes['sku'] = Helpers::convertFaNumbersToEn($value);
	}

	public function setPublishedAtAttribute($date)
	{
		if ($date == null) return;

		$carbonDate = is_numeric($date) ? Carbon::createFromTimestamp($date)->toDateTimeString() : $date;
		$this->attributes['published_at'] = $carbonDate;
	}

	public function setDiscountTypeAttribute($discountType)
	{
		if ($discountType != null && !in_array($discountType, static::getAvailableDiscountTypes())) {
			throw  Helpers::makeValidationException('نوع تخفیف وارد شده نامعتبر است');
		}
		$this->attributes['discount_type'] = $discountType;
	}
	//=================== setters ===================\\


	//=================== assign property for storing product ===================\\
	public function assignVariety($productRequest, $update = false)
	{
		if (!empty($varieties = $productRequest['varieties'])) {
			if ($update) {
				Variety::updateVarieties($varieties, $this);
			} else {
				Variety::storeVarieties($varieties, $this);
			}
		} else {
			Variety::storeFakeVariety($this, $productRequest['quantity']);
		}
	}

	public function assignSpecifications(array $product)
	{
		if (isset($product['specifications']) && !empty($product = $product['specifications'])) {
			$this->specifications()->detach();
			foreach ($product as $specification) {
				$value = $specification['value'];

				$specificationModel = Specification::whereId($specification['id'])->first();
				if ($specificationModel->type == 'select') {
					#زمانی که انتخابی است value == id است
					$specificationValueModel = SpecificationValue::find($value);
					$this->specifications()
						->attach($specificationModel->id, ['specification_value_id' => $specificationValueModel->id]);
				} elseif ($specificationModel->type == 'text') {
					$this->specifications()
						->attach($specificationModel->id, ['value' => $value]);
				} elseif ($specificationModel->type == 'multi_select') {
					$this->specifications()
						->attach($specificationModel->id);

					$productSpecificationPivot = $this->specifications()->where('specification_id', $specificationModel->id)
						->first()->pivot;

					$productSpecificationPivot->specificationValues()->sync($value);
				}
			}
		}
	}

	public function assignSizeChart($product)
	{
		if (!empty($sizeChartsRequest = $product['size_charts'])) {
			SizeChart::storeSizeCharts($sizeChartsRequest, $this);
		} else {
			SizeChart::query()->where('product_id', $this->id)->delete();
		}
	}

	public function assignGifts($product)
	{
		if (isset($product['gifts'])) {
			$gitPivots = [];
			foreach ($product['gifts'] as $gift) {
				$gitPivots[$gift['id']] = ['should_merge' => $gift['should_merge']];
			}
			$this->gifts()->sync($gitPivots);
		}
	}
	//=================== assign property for storing product ===================\\


	//=================== other functions ===================\\
	public function isAvailable()
	{
		return in_array($this->status, [static::STATUS_AVAILABLE]);
	}

	public function activeFlash()
	{
		return $this->flashes()->active()->latest()->whereColumn('sales_count', '<', 'salable_max');
	}

	public function hasFakeVariety()
	{
		foreach ($this->varieties as $variety) {
			if ($variety->isFake()) {
				return true;
			}
		}

		return false;
	}

	// public function beforeToArray()
	// {
	// 	if ($this->relationLoaded('varieties')) {
	// 		/** @var Variety $variety */
	// 		foreach ($this->varieties as $variety) {
	// 			$copyProduct = $this->replicate()->withoutRelations();
	// 			if ($this->relationLoaded('activeFlash')) {
	// 				$copyProduct->setRelation('activeFlash', $this->activeFlash);
	// 			}
	// 			if ($this->relationLoaded('activeGifts')) {
	// 				$copyProduct->setRelation('activeGifts', $this->activeGifts);
	// 			}
	// 			$variety->product()->associate($copyProduct);
	// 			$variety->dontToArrayProduct = true;
	// 		}
	// 	}
	// 	if (!(\Auth::user() instanceof Admin)) {
	// 		$other = [];
	// 		if (!Route::getCurrentRoute() || !str_contains(Route::getCurrentRoute()->getName(), 'show')) {
	// 			// اگر اعلام نکردیم که به اینا نیاز داریم پس پنهانش کن
	// 			$withSetting = app(CoreSettings::class)->get('product.with') ?? [];
	// 			foreach (['description', 'categories'] as $key) {
	// 				if (!in_array($key, $withSetting) && !in_array($key, $this->dontHide)) {
	// 					$other[] = $key;
	// 				}
	// 			}
	// 		}

	// 		static::makeHiddenForFront($this, $other);
	// 	}
	// }

	// public function afterToArray($result)
	// {
	// 	foreach ($result['categories'] ?? [] as $key1 => $cat) {
	// 		foreach (['description', 'meta_title', 'meta_description'] as $key) {
	// 			unset($result['categories'][$key1][$key]);
	// 		}
	// 	}
	// 	if ($this->relationLoaded('varieties') && isset($result['varieties'])) {
	// 		/** @var Variety $variety */
	// 		foreach ($result['varieties'] as $key => $variety) {
	// 			$tempCopy = $result;
	// 			$result['id'] = $this->id;
	// 			$except = ['activeFlash', 'activeGifts'];
	// 			foreach (array_filter(array_keys($this->relations), fn($v) => !in_array($v, $except)) as $relation) {
	// 				unset($tempCopy[\Illuminate\Support\Str::snake($relation)]);
	// 			}

	// 			$result['varieties'][$key]['product'] = $tempCopy;
	// 		}
	// 	}

	// 	return $result;
	// }

	// public function toArray()
	// {
	// 	// $this->beforeToArray();
	// 	$result = parent::toArray();

	// 	// return $this->afterToArray($result);
	// }

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('images');
		$this->addMediaCollection('video')->singleFile();
		$this->addMediaCollection('video_cover')->singleFile();
	}

	public function addVideoCover($image): void
	{
		if (empty($image)) return;
		Media::addMedia($image, $this, 'video_cover');
	}

	public function updateVideoCover($image): void
	{
		$updatedImage = Media::updateMedia($image, $this, 'video_cover');
		$mediaToDelete = $this->media()->where('collection_name', 'video_cover')->whereNotIn('id', $updatedImage)->get();
		foreach ($mediaToDelete as $media) {
			$media->delete();
		}
		$this->load('media');
	}

	public function addVideo($video): void
	{
		// dd($video);
		if (empty($video)) dd('fjeoi');
		Media::addMedia($video, $this, 'video');
	}

	public function updateVideo($video): void
	{
		$updatedVideo = Media::updateMedia($video, $this, 'video');
		$mediaToDelete = $this->media()->where('collection_name', 'video')->whereNotIn('id', $updatedVideo)->get();
		foreach ($mediaToDelete as $media) {
			$media->delete();
		}
		$this->load('media');
	}
	//=================== other functions ===================\\


	//=================== relations ===================\\
	public function categories(): BelongsToMany
	{
		return $this->belongsToMany(Category::class);
	}

	public function specifications(): BelongsToMany
	{
		return $this->belongsToMany(Specification::class)
			->latest('order')
			->using(ProductSpecificationPivot::class)
			->withPivot('id', 'value', 'specification_value_id');
	}

	public function brand(): BelongsTo
	{
		return $this->belongsTo(Brand::class);
	}

	public function sizeCharts(): HasMany
	{
		return $this->hasMany(SizeChart::class);
	}

	public function unit(): BelongsTo
	{
		return $this->belongsTo(Unit::class);
	}

	public function varieties(): HasMany
	{
		return $this->hasMany(Variety::class)->orderBy('order', 'DESC');
	}

	public function prettyVariety(): HasMany
	{
		return $this->hasMany(PrettyVariety::class);
	}

	public function productComments(): HasMany
	{
		return $this->hasMany(ProductComment::class);
	}

	public function flashes(): BelongsToMany
	{
		return $this->belongsToMany(Flash::class)
			->withPivot(['discount_type', 'discount', 'salable_max', 'sales_count']);
	}

	public function orderItems(): HasMany
	{
		return $this->hasMany(OrderItem::class);
	}

	public function favorites(): BelongsToMany
	{
		return $this->belongsToMany(Customer::class, 'favorites');
	}

	public function sets(): BelongsToMany
	{
		return $this->belongsToMany(\Modules\Product\Entities\ProductSet::class, 'product_set_product');
	}

	public function recommendations()
	{
		return $this->hasMany(\Modules\Product\Entities\Recommendation::class);
	}

	public function gifts()
	{
		return $this->belongsToMany(Gift::class, 'gift_product_variety', 'product_id')
			->withPivot('should_merge');
	}

	public function activeGifts()
	{
		return $this->gifts()->active();
	}

	public function categoriesIndex(): BelongsToMany
	{
		return $this->belongsToMany(Category::class, 'category_product_index');
	}

	public function imagesNew(): HasMany
	{
		$images = $this->hasMany(Media::class, 'model_id', 'id')
			->where('model_type', 'Modules\Product\Entities\Product')
			->select(DB::raw("concat(uuid, '/', file_name) as url"), 'model_id');
		return $images;
	}

	public function varietyOnlyDiscountsRelationship(): HasMany
	{
		return $this->varieties()->select(
			'varieties.id',
			'varieties.product_id',
			'varieties.discount',
			'varieties.discount_type',
			'varieties.price'
		)
			->with('product.activeFlash');
	}

	public function varietyOnlyIdsRelationship(): HasMany
	{
		return $this->varieties()->select('id');
	}
	//=================== relations ===================\\
}

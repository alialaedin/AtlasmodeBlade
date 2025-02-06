<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Classes\DontAppend;
use Modules\Order\Entities\OrderItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\Store\Entities\VarietyTransfer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Admin\Entities\Admin;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeValue;
use Modules\Cart\Entities\Cart;
use Modules\Color\Entities\Color;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Core\Transformers\MediaResource;
use Modules\Store\Entities\Store;
use Spatie\MediaLibrary\HasMedia;
use Modules\Core\Entities\Media;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\VarietyAttributeValuePivot;

class Variety extends BaseModel implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;
    protected $appends = ['unique_attributes_key', 'images', 'quantity', 'final_price', 'final_gifts', 'pending_for_exit_count', 'exited_count'];
    protected $hidden = ['media'];

    public $dontToArrayProduct = false;

    protected $fillable = [
        'name',
        'description',
        'price',
        'SKU',
        'barcode',
        'purchase_price',
        'discount_until',
        'discount_type',
        'discount',
        'order',
        'max_number_purchases'
    ];

    const ACCEPTED_IMAGE_MIMES = 'gif|png|jpg|jpeg|svg|webp';
    const DISCOUNT_TYPE_PERCENTAGE = "percentage";
    const DISCOUNT_TYPE_FLAT = "flat";

    protected $loadDiscount = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $with = app(CoreSettings::class)->get('product.variety.front.with');
        $gift = app(CoreSettings::class)->get('product.gift.active');
        if (!empty($with)) {
            $this->with = array_merge($this->with, $with);
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
        parent::booted();

        static::updating(function ($variety) {
            if ($variety->isDirty('max_number_purchases')) {
                Cache::forget('variety-quantity-' . $variety->id);
            }
        });

        static::deleting(function (\Modules\Product\Entities\Variety $variety) {
            if (!$variety->orderItems()->exists() && !$variety->isForceDeleting()) {
                $variety->forceDelete();
            }

            Cart::query()->where('variety_id', $variety->id)->get()->each(function ($cart) {
                $cart->delete();
            });
        });

        static::creating(function ($variety) {
            if ($variety->isDirty('max_number_purchases')) {
                Cache::forget('variety-quantity-' . $variety->id);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
    public function scopeNotActive($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    protected function getArrayableRelations()
    {
        $result = $this->getArrayableItems($this->relations);
        if ($this->dontToArrayProduct) {
            unset($result['product']);
        }

        return $result;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function addImages($images)
    {
        Media::addMedia($images, $this, 'images');
    }

    public function updateImages($images): void
    {
        $updatedImages = Media::updateMedia($images, $this, 'images');
        $mediaToDelete = $this->media()->whereNotIn('id', $updatedImages)->get();
        foreach ($mediaToDelete as $media) {
            $media->delete();
        }
    }

    public function getAvailableDiscountTypes(): array
    {
        return [static::DISCOUNT_TYPE_FLAT, static::DISCOUNT_TYPE_PERCENTAGE];
    }

    public static function storeVarieties($varietyRequest, Product $product)
    {
        foreach ($varietyRequest as $variety) {
            static::storeVariety($variety, $product);
        }
    }

    public static function storeVariety($varietyRequest, Product $product)
    {
        $variety = new static;
        $variety->fill($varietyRequest);
        $variety->product()->associate($product);
        if ($varietyRequest['color_id']) {
            $variety->color()->associate($varietyRequest['color_id']);
        }
        $variety->save();
        $store = (new self)->setStoreParams(
            $variety->id,
            Store::TYPE_INCREMENT,
            'موجودی اولیه محصول',
            $varietyRequest['quantity']
        );
        Store::insertModel($store, true);
        $variety->addImages($varietyRequest['images']);

        if ($attributes = $varietyRequest['attributes']) {
            $variety->assignAttributes($attributes);
        }
        $variety->assignGifts($varietyRequest);

        return $variety;
    }

    public static function updateVarieties($varietyRequest, Product $product)
    {
        $varietyIds = [];
        foreach ($varietyRequest as $variety) {
            if (isset($variety['id']) && $variety['id']) {
                $varietyIds[] = $variety['id'];
            }
            $variety = static::updateVariety($variety, $product);
            if ($variety != null) {
                $varietyIds[] = $variety->id;
            }
        }
        /**
         * زمانی که محصول آپدیت میشود اگر ایدی های تنوع ارسال نشه به این معنی هست که اون تنوع حذف شده.
         */
        $product->varieties()->whereNotIn('varieties.id', $varietyIds)
            ->get()->map(function ($v) {
                $v->delete();
            });
    }

    public static function updateVariety($varietyRequest, Product $product)
    {
        // اگر شناسه null باشد به این معنی است که تنوع جدید داره ثبت میشود.
        if (!isset($varietyRequest['id']) || !$varietyRequest['id']) {
            (new self)->deleteFakeVariety($product);
            return static::storeVariety($varietyRequest, $product);
        }

        $variety = static::query()->findOrFail($varietyRequest['id']);

        $variety->fill($varietyRequest);
        if ($varietyRequest['color_id']) {
            $variety->color()->associate($varietyRequest['color_id']);
        }
        $variety->save();

        $store = (new static)->updateStore($variety, $varietyRequest['quantity']);
        Store::insertModel($store);

        $variety->updateImages($varietyRequest['images']);

        if ($attributes = $varietyRequest['attributes']) {
            $variety->assignAttributes($attributes);
        }

        $variety->assignGifts($varietyRequest);
    }

    public function assignGifts($varietyRequest)
    {
        if (isset($varietyRequest['gifts'])) {
            foreach ($varietyRequest['gifts'] as $gift) {
                $this->gifts()->sync($gift['id']);
            }
        }
    }

    public function assignAttributes(array $attributes)
    {
        $this->attributes()->detach();
        foreach ($attributes as $attribute) {
            if (is_integer($attribute['value'])) {
                $attributeValue = AttributeValue::query()->where('id', $attribute['value'])->first();
                $this->attributes()->attach($attribute['id'], ['attribute_value_id' => $attributeValue->id, 'value' => $attributeValue->value]);
            }
            if (is_string($attribute['value'])) {
                $this->attributes()->attach($attribute['id'], ['value' => $attribute['value']]);
            }
        }
    }

    public static function storeFakeVariety(Product $product, $quantity)
    {
        $variety = $product->varieties()->first();
        if ($variety) {
            if (!$variety->isFake()) {
                $product->varieties()->delete();
                $variety = new static;
                $variety->product()->associate($product);
            }
        } else {
            $variety = new static;
            $variety->product()->associate($product);
        }
        $updating = (bool)$variety->id;
        $variety->fill([
            'price' => $product->unit_price,
            'SKU' => $product->SKU,
            'barcode' => $product->barcode,
            'purchase_price' => $product->purchase_price,
            'discount_type' => $product->discount_type,
            'discount' => $product->discount,
        ]);
        $variety->save();
        if (!$updating) {
            $store = (new self)->setStoreParams(
                $variety->id,
                Store::TYPE_INCREMENT,
                'موجودی اولیه محصول',
                $quantity
            );
        } else {
            $store = (new static)->updateStore($variety, $quantity);
        }

        Store::insertModel($store, true);
    }

    public function deleteFakeVariety($product)
    {
        $fakeVariety = $product->varieties()->get();
        $fakeVariety->each(function ($item) use ($fakeVariety) {
            /** @var $item static */
            if ($item->isFake()) {
                $item->delete();
            }
        });
    }

    public function isFake()
    {
        return !$this->attributes()->exists() && $this->color_id == null;
    }

    public function updateStore($variety, $quantity)
    {
        $store = Store::query()->where('variety_id', $variety->id)->first();
        if (!$store) {
            $store = $variety->store()->create([
                'balance' => 0
            ]);
        }
        $balance = $store->balance;

        if ($balance > $quantity) {
            $diff =  $balance - $quantity; //decrement
            $store = (new self)->setStoreParams(
                $variety->id,
                Store::TYPE_DECREMENT,
                "بروزرسانی موجودی محصول کاهش {$diff} عددی",
                ($diff < 0) ? 0 : $diff
            );
        } else if ($balance < $quantity) {
            $diff = $quantity - $balance; // increment
            $store = (new self)->setStoreParams(
                $variety->id,
                Store::TYPE_INCREMENT,
                "بروزرسانی موجودی محصول افزایش {$diff} عددی",
                $diff
            );
        } else {
            return null;
        }

        return $store;
    }

    public static function calculateDiscount($model, int $price, string $name): array
    {
        $appliedDiscountType = $name;
        if ($model->discount_type == static::DISCOUNT_TYPE_FLAT) {
            $appliedDiscountPrice = $model->discount;
            $discountType =  $model->discount_type;
        } else {
            $appliedDiscountPrice = (int)round(($model->discount * $price) / 100);
            $discountType =  static::DISCOUNT_TYPE_PERCENTAGE;
        }
        $finalPricePrice = $price - $appliedDiscountPrice;

        return [
            'discount_model'  => $appliedDiscountType,
            'discount_type'  => $discountType,
            'discount'  => $model->discount,
            'discount_price' => $appliedDiscountPrice,
            'amount'      => $finalPricePrice
        ];
    }

    public function setStoreParams(int $variety, string $type, string $description, int $quantity)
    {
        return (object)[
            'type' => $type,
            'description' => $description,
            'quantity' => $quantity,
            'variety_id' => $variety
        ];
    }

    public function setDiscountTypeAttribute($discountType)
    {
        if (($discountType != null) && !in_array($discountType, $this->getAvailableDiscountTypes())) {
            throw  Helpers::makeValidationException('نوع تخفیف وارد شده نامعتبر است');
        }
        $this->attributes['discount_type'] = $discountType;
    }

    public function setBarcodeAttribute($value)
    {
        $this->attributes['barcode'] = Helpers::convertFaNumbersToEn($value);
    }

    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = Helpers::convertFaNumbersToEn($value);
    }

    public function getQuantityAttribute()
    {
        return Cache::rememberForever('variety-quantity-' . $this->id, function () {
            $balance = $this->store->balance ?? 0;
            return min($balance, $this->max_number_purchases);
        });
    }

    public function getTotalSalesAttribute()
    {
        $startDateFilter = request()->has('start_date') && request()->filled('start_date') ? request('start_date') : null;
        $endDateFilter = request()->has('end_date') && request()->filled('end_date') ? request('end_date') : now();

        $ordersItems = $this->orderItems
            ->where('status', 1)
            ->where('created_at', '<=', $endDateFilter)
            ->when($startDateFilter, fn($q) => $q->where('created_at', '>=', $startDateFilter));

        $totalSalesAmount = $ordersItems->map(function ($item) {
            return ($item->amount - $item->discount_amount) * $item->quantity;
        })->sum();

        $totalSalesCount = $ordersItems->sum('quantity');

        return [
            'sales_count' => $totalSalesCount,
            'sales_amount' => $totalSalesAmount,
        ];
    }

    public function getUniqueAttributesKeyAttribute()
    {
        if (!$this->relationLoaded('attributes')) {
            return new DontAppend('Variety getUniqueKeyAttribute');
        }
        /** @var Collection $attributes */
        $attributes = $this->getRelation('attributes');
        $attributesString = $attributes->map(function ($attribute) {
            return $attribute->id . $attribute->pivot->value . $attribute->pivot->attribute_value_id;
        })->join('');

        return 'c' . $this->color_id . 'a' . $attributesString;
    }

    public function getImagesAttribute()
    {
        if (!$this->relationLoaded('media')) {
            return new DontAppend('Variety getImagesAttribute');
        }
        $allImages = $this->getMedia('images');
        if (!$allImages) {
            return null;
        }

        return MediaResource::collection($allImages);
    }

    public function getFinalPriceAttribute(): array
    {
        $product = $this->product;
        $variety = $this;

        $appliedDiscountType = 'none';
        $discountType = 'none';
        $flash = $product->activeFlash->first();
        $discount = 0;

        if ($flash) {
            $discount = $flash->discount;
            $discountType = $flash->discount_type;
            $appliedDiscountType = 'flash';
        } elseif ($variety->discount && $variety->discount_until >= now()) {
            $discount = $variety->discount;
            $discountType = $variety->discount_type;
            $appliedDiscountType = 'variety';
        }
        // discount on variety is not important here.
        $final_price = $variety->price;
        $discount_price = 0;
        $discount_colleague_price = 'none';
        $final_colleague_price = $variety->colleague_price ?? 'none';
        // calculate discount_price and discount_colleague_price.
        if ($discount != 0) {
            if ($discountType == 'percentage') {
                $discount_price = (int)round(($discount * $variety->price) / 100);
            } else {
                $discount_price = $discount;
            }
            // calculate final_price
            $final_price = $variety->price - $discount_price;

            // colleague_price should define in product to calculate.
            if ($variety->colleague_price) {
                if ($discountType == 'percentage') {
                    $discount_colleague_price = (int)round(($discount * $variety->colleague_price) / 100);
                } else {
                    $discount_colleague_price = $discount;
                }
                // calculate final_colleague_price.
                $final_colleague_price = $variety->colleague_price - $discount_colleague_price;
                // if colleague price with discount was less than purchase price we sell by final_price of customers price
                if ($variety->purchase_price && $final_colleague_price < $variety->purchase_price)
                    $final_colleague_price = $final_price;
            }
        }
        return [
            'base_amount' => $variety->price,
            'base_colleague_amount' => $variety->colleague_price ?? 'none',
            'discount_model'  => $appliedDiscountType,
            'discount_type'  => $discountType,
            'discount'  => $discount,
            'discount_price' => $discount_price,
            'discount_colleague_price' => $discount_colleague_price,
            'colleague_amount' => $final_colleague_price,
            'amount' => $final_price
        ];
    }

    // public function getFinalPriceAttribute()
    // {
    //     if (!$this->relationLoaded('product') || !$this->product->relationLoaded('activeFlash')) {
    //         return new DontAppend('Variety getFinalPriceAttribute');
    //     }
    //     /**
    //      * @var $product Product
    //      */
    //     $product = $this->product->makeHidden(['total_quantity', 'price']);
    //     $flash = $product->activeFlash->first();

    //     $productPrice = $this->price;

    //     if ($flash !== null) {
    //         return static::calculateDiscount($flash->pivot, $productPrice, 'flash');
    //     } elseif ($this->discount_type != null) {
    //         return static::calculateDiscount($this, $productPrice, 'variety');
    //     } elseif ($product->discount_type != null) {
    //         return static::calculateDiscount($product, $productPrice, 'product');
    //     }

    //     return [
    //         'discount_model'  => 'none',
    //         'discount_type'  => 'none',
    //         'discount'  => 0,
    //         'discount_price' => 0,
    //         'amount'      => $productPrice
    //     ];
    // }

    public function getFinalGiftsAttribute()
    {
        if (!$this->relationLoaded('activeGifts')) {
            return new DontAppend('getFinalGiftsAttribute');
        }

        $finalGifts = collect();

        foreach ($this->activeGifts as $gift) {
            $finalGifts->push($gift);
        }

        foreach ($this->product->activeGifts as $gift) {
            if ($gift->pivot->should_merge) {
                $finalGifts->push($gift);
            }
        }

        return $finalGifts->unique();
    }

    public function getPendingForExitCountAttribute(): int|DontAppend
    {
        return $this->relationLoaded('pendingOrderItems') ? $this->pendingOrderItems->sum('quantity') : new DontAppend('PendingForExitCount');
    }

    public function getExitedCountAttribute(): int|DontAppend
    {
        return $this->relationLoaded('doneOrderItems') ? $this->doneOrderItems->sum('quantity') : new DontAppend('PendingForExitCount');
    }

    public function getMainImageAttribute()
    {
        $media = $this->getFirstMedia('images');
        if ($media) {
            return $media;
        }

        return null;
    }

    public function getTitleAttribute()
    {
        $title = $this->product->title . '|';
        $title .= $this->color->name ?? '';
        // if (!$this->relationLoaded('attributes')) {
        //     return new DontAppend('Variety getTitleAttribute');
        // }
        foreach ($this->relations['attributes'] ?? [] as $attribute) {
            $title .= ' | ' . $attribute->label . ': ' . $attribute->pivot->value;
        }

        return $title;
    }

    public function pendingOrderItems(): HasMany
    {
        $request = request();
        Helpers::toCarbonRequest(['start_date', 'end_date'], $request);

        return $this->hasMany(OrderItem::class)
            ->whereHas('order', function ($order_query) {
                $order_query->whereNotIn('status', ['delivered']);
            })
            ->where(function ($q) {
                $q->where('is_done', false)->orWhereNull('is_done');
            })
            ->when($request->filled('start_date'), function (Builder $q) use ($request) {
                $q->where('order_items.created_at', '>', $request->start_date);
            })
            ->when($request->filled('end_date'), function (Builder $q) use ($request) {
                $q->where('order_items.created_at', '<', $request->end_date);
            });
    }

    public function doneOrderItems(): HasManyThrough
    {
        $request = request();
        Helpers::toCarbonRequest(['start_date', 'end_date'], $request);

        return $this->hasMany(OrderItem::class)
            ->whereHas('order', function ($order_query) {
                $order_query->whereNotIn('status', ['delivered']);
            })
            ->where(function ($q) {
                $q->where('is_done', true);
            })
            ->when($request->filled('start_date'), function (Builder $q) use ($request) {
                $q->where('order_items.updated_at', '>', $request->start_date);
            })
            ->when($request->filled('end_date'), function (Builder $q) use ($request) {
                $q->where('order_items.updated_at', '<', $request->end_date);
            });
    }

    public function transfers()
    {
        return $this->hasMany(VarietyTransfer::class, 'variety_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attribute_variety', 'variety_id')
            ->using(VarietyAttributeValuePivot::class)
            ->withPivot('attribute_value_id', 'value');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'variety_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variety_id');
    }

    public function gifts(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Product\Entities\Gift::class, 'gift_product_variety', 'variety_id')
            ->withPivot('should_merge');
    }

    public function activeGifts()
    {
        return $this->gifts()->active();
    }

    //    public static function calculateDiscount($model, int $price, string $name): array
    //    {
    //        $appliedDiscountType = $name;
    //        if ($model->discount_type == static::DISCOUNT_TYPE_FLAT){
    //            $appliedDiscountPrice = $model->discount;
    //            $discountType =  $model->discount_type;
    //        }else{
    //            $appliedDiscountPrice = (int)round(($model->discount * $price) / 100);
    //            $discountType =  static::DISCOUNT_TYPE_PERCENTAGE;
    //        }
    //        $finalPricePrice = $price;
    //
    //        return [
    //            'discount_model'  => $appliedDiscountType,
    //            'discount_type'  => $discountType,
    //            'discount'  => $model->discount,
    //            'discount_price' => $appliedDiscountPrice,
    //            'amount'      => $finalPricePrice
    //        ];
    //    }

}

<?php

namespace Modules\Category\Entities;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Admin\Entities\Admin;
use Modules\Attribute\Entities\Attribute;
use Modules\Brand\Entities\Brand;
use Modules\Core\Classes\DontAppend;
use Modules\Core\Exceptions\ModelCannotBeDeletedException;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Traits\HasDefaultFields;
use Modules\Core\Traits\HasViews;
use Modules\Core\Transformers\MediaResource;
use Modules\Product\Entities\Product;
use Modules\Specification\Entities\Specification;
use Spatie\EloquentSortable\SortableTrait;
use Modules\Core\Traits\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Category extends Model implements HasMedia
{
	use InteractsWithMedia, HasDefaultFields, HasAuthors, SortableTrait, HasViews, Sluggable;

	public $sortable = [
		'order_column_name' => 'order',
		'sort_when_creating' => true,
	];

	protected $fillable = [
		'title',
		'en_title',
		'description',
		'parent_id',
		'status',
		'special',
		'meta_title',
		'meta_description',
		'priority',
		'level',
		'show_in_home',
	];

	protected $cast = ['priority' => 'int'];
	protected $with = ['children'];
	protected $hidden = ['media'];

	private const FRONT_CATEGORIES_CACHE_KEY = 'allFrontCategories';
	private const ADMIN_CATEGORIES_CACHE_KEY = 'allAdminCategories';
	private const SPECIAL_CATEGORIES_CACHE_KEY = 'specialCategories';

	protected static function booted()
	{
		parent::booted();
		static::deleting(function ($category) {
			if ($category->products()->exists()) {
				throw new ModelCannotBeDeletedException('دسته بندی دارای محصول میباشد.');
			}
		});
		Helpers::clearCacheInBooted(static::class, self::FRONT_CATEGORIES_CACHE_KEY);
		Helpers::clearCacheInBooted(static::class, self::ADMIN_CATEGORIES_CACHE_KEY);
		Helpers::clearCacheInBooted(static::class, self::SPECIAL_CATEGORIES_CACHE_KEY);
	}

	public static function getCategoriesForAdmin()
	{
		return Cache::rememberForever(self::ADMIN_CATEGORIES_CACHE_KEY, function () {
			return self::query()
				->whereNull('parent_id')
				->orderByDesc('order')
				->with('children')
				->get();
		});
	}

	public static function getCategoriesForFront()
	{
		return Cache::rememberForever(self::FRONT_CATEGORIES_CACHE_KEY, function () {
			$selectedColumns = ['id', 'title', 'priority', 'parent_id'];
			return self::query()
				->select($selectedColumns)
				->orderByDesc('priority')
				->with([
					'children' => function ($q) use ($selectedColumns) {
						$q->select($selectedColumns)
							->orderByDesc('priority')
							->active()
							->with('children', fn($q) => $q->active()->select($selectedColumns));
					}
				])
				->parents()
				->active()
				->get();
		});
	}

	public static function getSpecialCategories()
	{
		return Cache::rememberForever(self::SPECIAL_CATEGORIES_CACHE_KEY, function () {
			return self::query()
				->select(['id', 'title', 'slug', 'status', 'special'])
				->take(8)
				->special()
				->active()
				->latest('id')
				->with('media')
				->get()
				->each(fn(self $category) => $category->append(['image']));
		});
	}

	public static function getCategoriesToSetParent(self|null $category = null)
	{
		return self::query()
			->select(['id', 'title', 'order'])
			->orderByDesc('order')
			->when($category, fn($q) => $q->whereKeyNot($category->id))
			->get();
	}

	public static function storeOrUpdate(Request $request, self|null $category = null)
	{
		$isUpdating = (bool) $category;
		$data = $request->validated();

		if ($isUpdating) {
			$category->update($data);
			ActivityLogHelper::updatedModel('دسته بندی بروز شد', $category);
		} else {
			$category = self::createCategory($data, $request->filled('parent_id') ? $request->parent_id : null);
			ActivityLogHelper::storeModel('دسته بندی ثبت شد', $category);
		}

		self::syncRelationships($category, $request);
		self::handleFiles($request, $category);

		return $category;
	}

	private static function createCategory(array $data, $parentId = null): self
	{
		$category = new self();
		$category->fill($data);

		if ($parentId) {
			$parentLevel = DB::table('categories')->where('id', $parentId)->value('level');
			$category->level = $parentLevel ? $parentLevel + 1 : 1;
		}

		$category->save();
		return $category;
	}

	private static function syncRelationships(self $category, Request $request): void
	{
		$category->attributes()->sync($request->attribute_ids ?? []);
		$category->specifications()->sync($request->specification_ids ?? []);
		$category->brands()->sync($request->brand_ids ?? []);
	}

	private static function handleFiles(Request $request, self $category): void
	{
		if ($request->hasFile('icon')) {
			$category->clearMediaCollection('icon');
			$category->addIcon($request->file('icon'));
		}
		if ($request->hasFile('image')) {
			$category->clearMediaCollection('image');
			$category->addImage($request->file('image'));
		}
	}

	public static function sort(array $categories, $parentId = null)
  {
    $order = 999999;
    foreach ($categories as $categoryArr) {
      $category = self::find($categoryArr['id']);
      if (!$category) {
        continue;
      }

      $category->update([
        'order' => $order--,
        'parent_id' => $parentId,
      ]);

      if (!empty($categoryArr['children'])) {
        self::sort($categoryArr['children'], $category->id);
      }
    }
  }

	public function sluggable(): array
	{
		return [
			'slug' => [
				'source' => 'en_title'
			]
		];
	}

	public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(Category::class, 'parent_id');
	}

	public function attributes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Attribute::class)->withTimestamps();
	}

	public function specifications(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Specification::class)->withTimestamps();
	}

	public function brands(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Brand::class)->withTimestamps();
	}

	public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(Category::class, 'parent_id', 'id');
		// ->orderBy('priority', 'DESC')
		// ->with(['children', 'attributes.values', 'brands', 'specifications.values']);
	}

	public function scopeParents($query, $parent_id = null)
	{
		return $query->where('parent_id', $parent_id);
	}

	public function scopeSpecial($query)
	{
		return $query->where('special', true);
	}

	public function scopeActive($query)
	{
		return $query->where('status', true);
	}

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('images')->singleFile();
		$this->addMediaCollection('icons')->singleFile();
	}

	public function addImage($file)
	{
		$this->addMedia($file)
			->withCustomProperties(['type' => 'category'])
			->toMediaCollection('images');
	}

	public function addIcon($file)
	{
		$this->addMedia($file)
			->withCustomProperties(['type' => 'category'])
			->toMediaCollection('icons');
	}

	public function getImageAttribute()
	{
		if (!$this->relationLoaded('media')) {
			return new DontAppend('Category getImageAttribute');
		}
		$image = $this->getFirstMedia('images');
		if (!$image) return null;

		return new MediaResource($image);
	}

	public function getIconAttribute()
	{
		if (!$this->relationLoaded('media')) {
			return new DontAppend('Category getIconAttribute');
		}
		$icon = $this->getFirstMedia('icons');
		if (!$icon) return null;

		return new MediaResource($icon);
	}

	public function products()
	{
		$query = $this->belongsToMany(Product::class);
		if (!(Auth::user() instanceof Admin)) {
			$query->active();
		}
		return $query;
	}

	public static function getAllCategoriesForProductList()
	{
		return static::query()
			->select(['id', 'title', 'parent_id', 'status'])
			->parents()
			->active()
			->withCount('products')
			->with([
				'children' => fn($q) => $q->select(['id', 'title', 'parent_id'])->withCount('products')
			])
			->get();
	}
}

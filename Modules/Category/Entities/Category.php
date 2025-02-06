<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Admin;
use Modules\Attribute\Entities\Attribute;
use Modules\Brand\Entities\Brand;
use Modules\Core\Classes\DontAppend;
use Modules\Core\Entities\HasCommonRelations;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Traits\HasDefaultFields;
use Modules\Core\Traits\HasViews;
use Modules\Core\Transformers\MediaResource;
use Modules\Product\Entities\Product;
use Modules\Specification\Entities\Specification;
use Spatie\EloquentSortable\SortableTrait;
use Modules\Core\Traits\InteractsWithMedia;

class Category extends Model
{
    use InteractsWithMedia, HasDefaultFields, HasAuthors, SortableTrait, HasViews;

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

    protected static function booted()
    {
        parent::booted();
        static::deleting(function ($category) {
            if ($category->products()->exists()) {
                throw Helpers::makeValidationException('دسته بندی دارای محصول میباشد.');
            }
        });
        Helpers::clearCacheInBooted(static::class, 'home_category');
        Helpers::clearCacheInBooted(static::class, 'home_special_category');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
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
        return $this->hasMany(Category::class, 'parent_id', 'id')
            ->orderBy('priority', 'DESC')
            ->with(['children', 'attributes.values', 'brands', 'specifications.values']);
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

    public static function sort($categories)
    {
        $items = Category::whereIn('id', $categories)->get();
        $itemMap = $items->keyBy('id');

        foreach ($categories as $index => $id) {
            if (isset($itemMap[$id])) {
                $item = $itemMap[$id];
                $item->priority = $index + 1;
                $item->save();
            }
        }
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

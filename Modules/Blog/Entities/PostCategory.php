<?php

namespace Modules\Blog\Entities;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Exceptions\ModelCannotBeDeletedException;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Entities\BaseModel;
use Modules\Blog\Entities\Post;
use Modules\Core\Helpers\Helpers;

class PostCategory extends BaseModel implements Sortable
{
  use HasAuthors, SortableTrait, Sluggable;

  protected $fillable = ['name', 'slug', 'status'];
  public $sortable = [
    'order_column_name' => 'order',
    'sort_when_creating' => true,
  ];

  private const ADMIN_POST_CATEGORIES_CACHE_KEY = 'adminPostCategories';
  private const FRONT_POST_CATEGORIES_CACHE_KEY = 'frontPostCategories';

  public function sluggable(): array
  {
    return [
      'slug' => [
        'source' => 'name'
      ]
    ];
  }

  public static function booted(): void
  {
    Helpers::clearCacheInBooted(self::class, self::ADMIN_POST_CATEGORIES_CACHE_KEY);
    Helpers::clearCacheInBooted(self::class, self::FRONT_POST_CATEGORIES_CACHE_KEY);

    static::deleting(function (PostCategory $postCategory) {
      if ($postCategory->posts()->count() > 0) {
        throw new ModelCannotBeDeletedException('این دسته بندی دارای مطلب می باشد و نمی تواند حذف شود.');
      }
    });
  }

  public static function getAllPostCategoriesForAdmin()
  {
    return Cache::rememberForever(self::ADMIN_POST_CATEGORIES_CACHE_KEY, function () {
      return self::query()
        ->select(['id', 'name', 'status', 'order', 'created_at'])
        ->latest('id')
        ->withCount('posts')
        ->get();
    });
  }

  public static function getAllPostCategoriesForFront()
  {
    return Cache::rememberForever(self::FRONT_POST_CATEGORIES_CACHE_KEY, function () {
      return self::query()
        ->select(['id', 'name', 'status', 'slug'])
        ->latest('id')
        ->active()
        ->get();
    });
  }

  public function scopeActive($query)
  {
    return $query->where('status', 1);
  }

  public function getIsDeletableAttribute(): bool
  {
    return $this->posts()->count() === 0;
  }

  public function posts()
  {
    return $this->hasMany(Post::class, 'post_category_id');
  }
}

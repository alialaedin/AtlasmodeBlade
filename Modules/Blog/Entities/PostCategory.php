<?php

namespace Modules\Blog\Entities;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Exceptions\ModelCannotBeDeletedException;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Entities\BaseModel;
use Modules\Blog\Entities\Post;

class PostCategory extends BaseModel implements Sortable
{
  use HasAuthors, SortableTrait;

  protected $fillable = ['name','slug','status'];

  public $sortable = [
    'order_column_name' => 'order',
    'sort_when_creating' => true,
  ];

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
    static::deleting(function (PostCategory $postCategory) {
      if ($postCategory->posts()->count() > 0) {
        throw new ModelCannotBeDeletedException('این دسته بندی دارای مطلب می باشد و نمی تواند حذف شود.');
      }
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

  public static function getActiveCategories()
  {
    return static::query()->select(['id', 'name', 'status'])->active()->get();
  }

  public static function getAllCategories()
  {
    return static::query()->select(['id', 'name', 'status'])->get();
  }

  public function scopeFilters($query)
  {
    $status = request('status');

    return $query
      ->when(request('id'), function (Builder $query) {
        $query->where('id', request('id'));
      })
      ->when(request('title'), function (Builder $query) {
        $query->where('title', 'LIKE', '%' . request('title') . '%');
      })
      ->when(isset($status), fn($query) => $query->where("status", $status))
      ->when(request('start_date'), function (Builder $query) {
        $query->whereDate('created_at', '>=', request('start_date'));
      })
      ->when(request('end_date'), function (Builder $query) {
        $query->whereDate('created_at', '<=', request('end_date'));
      });
  }

  //Relations

  public function posts()
  {
    return $this->hasMany(Post::class, 'post_category_id');
  }
}

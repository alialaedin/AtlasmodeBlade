<?php

namespace Modules\Blog\Entities;

use Cviebrock\EloquentSluggable\Sluggable;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Modules\Comment\Entities\Commentable;
use Modules\Comment\Entities\HasComment;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasMorphAuthors;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Core\Transformers\MediaResource;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Tags\HasTags;
use Modules\Blog\Entities\PostCategory;
use Modules\Comment\Entities\Comment;

class Post extends Model implements Sortable, HasMedia, HasComment, Viewable
{
  use HasMorphAuthors,
    Sluggable,
    SortableTrait,
    InteractsWithMedia,
    Commentable,
    HasTags,
    InteractsWithViews;

  const STATUS_DRAFT = 'draft';

  const STATUS_PENDING = 'pending';

  const STATUS_PUBLISHED = 'published';

  const STATUS_UNPUBLISHED = 'unpublished';

  protected  $appends = ['image', 'views_count'];

  public function getViewsCountAttribute()
  {
    return views($this)->count();
  }

  protected $hidden = ['media'];

  public $sortable = [
    'order_column_name' => 'order',
    'sort_when_creating' => true,
  ];

  protected $withCount = ['comments'];

  protected $fillable = [
    'title',
    'slug',
    'summary',
    'order',
    'body',
    'meta_description',
    'status',
    'special',
    'published_at'
  ];

  protected $dates = [
    'published_at'
  ];

  protected $allFields = [
    'id',
    'title',
    'slug',
    'summary',
    'order',
    'body',
    'meta_description',
    'status',
    'special',
    'published_at',
    'created_at',
    'updated_at',
    'creatorable_id',
    'updaterable_id'
  ];

  protected $longFields = [
    'body'
  ];

  public function sluggable(): array
  {
    return [
      'slug' => [
        'source' => 'title'
      ]
    ];
  }

  public static function booted()
  {
    static::deleted(function (Post $post) {
      $post->tags()->detach();
      $post->comments()->delete();
    });
    Helpers::clearCacheInBooted(static::class, 'home_post');
  }

  public static function getAvailableStatuses()
  {
    return [
      static::STATUS_DRAFT,
      static::STATUS_PENDING,
      static::STATUS_PUBLISHED,
      static::STATUS_UNPUBLISHED
    ];
  }

  //Media library

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('image')->singleFile();
  }

  public function addImage($file)
  {
    return $this->addMedia($file)
      ->withCustomProperties(['type' => 'post'])
      ->toMediaCollection('image');
  }

  public function getImageAttribute(): ?MediaResource
  {
    $media = $this->getFirstMedia('image');
    if (!$media) {
      return null;
    }
    return new MediaResource($media);
  }

  public function scopePublished($query)
  {
    $query->where('status', static::STATUS_PUBLISHED)
      ->whereDate('published_at', '<=', now());
  }

  public function scopeFilters($query)
  {
    return $query
      ->when(request('id'), fn($q) => $q->where('id', request('id')))
      ->when(request('title'), fn($q) => $q->where('title', 'LIKE', '%' . request('title') . '%'))
      ->when(request('post_category_id'), function ($q) {
        if (request('post_category_id') != 'all') {
          $q->where('post_category_id', request('post_category_id'));
        }
      })
      ->when(request('status'), function ($q) {
        if (request('status') != 'all') {
          $q->where('status', request('status'));
        }
      })
      ->when(request('start_date'), fn($q) => $q->whereDate('created_at', '>=', request('start_date')))
      ->when(request('end_date'), fn($q) => $q->whereDate('created_at', '<=', request('end_date')));
  }



  //Relations

  public function category()
  {
    return $this->belongsTo(PostCategory::class, 'post_category_id');
  }

  public function comments()
  {
    return $this->hasMany(Comment::class, 'commentable_id')->where('commentable_type', Post::class);
  }

  public function scopeIndex($query, $longFields = null)
  {
    $longFields = $longFields ?? $this->longFields;

    $query->select(array_diff($this->allFields, $longFields));
  }
}

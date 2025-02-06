<?php

namespace Modules\Slider\Entities;

use Modules\Core\Entities\BaseModel;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Transformers\MediaResource;
use Modules\Link\Traits\HasLinks;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Modules\Core\Traits\InteractsWithMedia;

class Slider extends BaseModel implements HasMedia
{
  use InteractsWithMedia, HasLinks, SortableTrait, HasAuthors;

  // protected $with = [
  //   'media'
  // ];

  public $sortable = [
    'order_column_name' => 'order',
    'sort_when_creating' => true,
  ];

  protected $casts = ['status' => 'boolean'];

  // protected $appends = ['group_label', 'image', 'unique_type'];

  protected $fillable = [
    'title',
    'description',
    'group',
    'link',
    'status',
    'custom_fields'
  ];

  protected $hidden = ['media'];

  protected static function booted()
  {
    parent::booted();

    Helpers::clearCacheInBooted(static::class, 'home_slider');
  }

  public function getGroupLabelAttribute()
  {
    return __('core::groups.' . $this->group);
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('main')->singleFile();
  }

  public function getImageAttribute()
  {
    $media = $this->getMedia('main')->first();

    return new MediaResource($media);
  }

  public function addImage($image)
  {
    if (!$image) {
      return;
    }

    return $this->addMedia($image)->toMediaCollection('main');
  }

  public function scopeActive($query)
  {
    return $query->where('status', 1);
  }

  public function getUniqueTypeAttribute()
  {
    if (!$this->linkable_type) {
      return 'link_url';
    }
    if ($this->linkable_id) {
      return $this->linkable_type;
    } else {
      return 'Index' . $this->linkable_type;
    }
  }
}

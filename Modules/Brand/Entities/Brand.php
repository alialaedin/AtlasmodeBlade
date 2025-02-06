<?php

namespace Modules\Brand\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Transformers\MediaResource;
use Modules\Product\Entities\Product;
use Spatie\MediaLibrary\HasMedia;
use Modules\Core\Traits\InteractsWithMedia;

class Brand extends BaseModel implements HasMedia
{
  use HasFactory, InteractsWithMedia, HasAuthors;

  protected  $appends = ['image'];
  protected $hidden = ['media'];

  protected $fillable = [
    'name',
    'status',
    'show_index',
    'description',
  ];

  protected $dependantRelations = []; //TODO Product

  protected static function booted()
  {
    static::deleting(function (Brand $brand) {
      if ($brand->products()->exists()) {
        throw Helpers::makeValidationException('به علت وجود محصولی با این برند امکان حذف آن وحود ندارد');
      }
    });
  }

  public function scopeActive($query)
  {
    return $query->where('status', true);
  }

  public function scopeIndexActive($query)
  {
    return $query->where('show_index', true);
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('image')->singleFile();
  }

  public function addImage($file)
  {
    return $this->addMedia($file)
      ->withCustomProperties(['type' => 'brand'])
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

  public function products()
  {
    return $this->hasMany(Product::class);
  }
}

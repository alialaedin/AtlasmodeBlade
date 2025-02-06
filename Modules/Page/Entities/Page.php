<?php

namespace Modules\Page\Entities;

use Modules\Core\Entities\BaseModel;
use Modules\Core\Services\Media\MediaDisplay;

class Page extends BaseModel
{
  protected $fillable = [
    'title',
    'text',
    'slug'
  ];

  public function sluggable(): array
  {
    $slug = empty($this->slug) ? 'title' : 'slug';

    return [
      'slug' => [
        'source' => $slug
      ]
    ];
  }

  public function getTextAttribute($value):string|null {
    return MediaDisplay::ckfinderImageConverter($value);
}
}

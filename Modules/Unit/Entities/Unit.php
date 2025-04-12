<?php

namespace Modules\Unit\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Exceptions\ModelCannotBeDeletedException;
use Modules\Core\Traits\HasAuthors;
use Modules\Product\Entities\Product;

class Unit extends Model
{
  use HasAuthors;

  public const PRECISION = ['1', '0.1', '0.01'];

  protected $fillable = [
    'name',
    'symbol',
    'precision',
    'status'
  ];

  protected $hidden = ['created_at', 'updated_at', 'creator_id', 'updater_id'];
  protected $appends = ['is_deletable'];

  public static function booted(): void
  {
    static::deleting(function (Unit $unit) {
      if (!$unit->is_deletable) {
        throw new ModelCannotBeDeletedException('این واحد به محصولی متصل است و قابل حذف نمی باشد.');
      }
    });
  }

  public static function getAvailablePrecisions()
  {
    return self::PRECISION;
  }

  public function scopeActive($query)
  {
    return $query->where('status', 1);
  }

  public function getIsDeletableAttribute()
  {
    return $this->products->isEmpty();
  }

  public function products()
  {
    return $this->hasMany(Product::class); 
  }
}

<?php

namespace Modules\Unit\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasAuthors;

class Unit extends Model
{
  use HasAuthors;

  const PRECISION = ['1', '0.1', '0.01'];

  protected $fillable = [
    'name',
    'symbol',
    'precision',
    'status'
  ];

  protected $hidden = ['created_at', 'updated_at', 'creator_id', 'updater_id'];

  public static function booted(): void
  {
    static::deleting(function (Unit $unit) {
      //check conditions
    });
  }

  public function scopeActive($query)
  {
    return $query->where('status', 1);
  }
}

<?php

namespace Modules\Newsletters\Entities;

use Illuminate\Database\Eloquent\Model;

class Newsletters extends Model
{
  protected $fillable = ['title', 'body', 'send_at', 'status'];

  const STATUS_PENDING = 'pending';
  const STATUS_SUCCESS = 'success';
  const STATUS_FAIL = 'fail';

  public static function getAvailableStatus(): array
  {
    return [static::STATUS_SUCCESS, static::STATUS_PENDING, static::STATUS_FAIL];
  }
  
  public function scopeFilters($query)
  {
    return $query;
  }

}

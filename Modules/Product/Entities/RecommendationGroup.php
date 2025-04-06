<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RecommendationGroup extends Model
{
  protected $fillable = ['name', 'label', 'show_in_home', 'show_in_filter'];
  const ALL_GROUPS_CACHE_kEY = 'allRecommendationGroups';

  protected static function booted()
  {
    static::created(fn () => Cache::forget(self::ALL_GROUPS_CACHE_kEY));
    static::updated(fn () => Cache::forget(self::ALL_GROUPS_CACHE_kEY));
    static::deleted(fn () => Cache::forget(self::ALL_GROUPS_CACHE_kEY));
  }

  public static function getAllGroups()
  {
    return Cache::rememberForever(self::ALL_GROUPS_CACHE_kEY, function () {
      return self::query()->withCount('items')->get();
    });
  }

  public function items()
  {
    return $this->hasMany(Recommendation::class, 'group_id'); 
  }
}

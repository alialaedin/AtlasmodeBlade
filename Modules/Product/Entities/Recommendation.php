<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasDefaultFields;
use Modules\Product\Entities\Product;

class Recommendation extends Model
{
  use HasDefaultFields;

  protected array $defaults = [
    'order' => 1
  ];

  protected $fillable = ['group_id', 'product_id', 'order'];

  protected static function booted()
  {
    static::created(fn () => Cache::forget(RecommendationGroup::ALL_GROUPS_CACHE_kEY));
    static::deleted(fn () => Cache::forget(RecommendationGroup::ALL_GROUPS_CACHE_kEY));
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function group()
  {
    return $this->belongsTo(RecommendationGroup::class, 'group_id');
  }

  public static function sort(Request $request)
  {
    $recommendationsIds = $request->ids;
    $recommendationGroupId = $request->group_id;
    $order = 999999;
    $recommendations = static::query()->where('group_id', $recommendationGroupId);
    foreach ($recommendationsIds as $id) {
      $recommendations->clone()->whereKey($id)->update(['order' => $order--]);
    }
    Helpers::clearCacheInBooted(static::class, 'home_recommendations');
  }
}

<?php

namespace Modules\Product\Entities;

use Modules\Core\Entities\BaseModel;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasDefaultFields;
use Modules\Product\Entities\Product;

class Recommendation extends BaseModel
{
  use HasDefaultFields;

  protected array $defaults = [
    'order' => 1
  ];

  protected $fillable = ['group'];

  protected static function booted()
  {
    parent::booted();
    Helpers::clearCacheInBooted(static::class, 'new_products');
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public static function store($request): static
  {
    $recommendation = new static($request->all());
    $product = Product::query()->findOrFail($request->input('product_id'));
    $recommendation->product()->associate($product);
    $recommendation->save();

    return $recommendation;
  }

  public static function sort(array $recommendationsIds, $recommendationGroup)
  {
    $order = 999999;
    $recommendations = static::query()->where('group', $recommendationGroup);
    foreach ($recommendationsIds as $id) {
      $recommendations->clone()->whereKey($id)->update(['order' => $order--]);
    }
    Helpers::clearCacheInBooted(static::class, 'home_recommendations');

    return $recommendations->get();
  }

  public static function get()
  {
    return static::query()
      ->with('product.varieties')
      ->latest('order')->filters()->paginate();
  }

  public function scopeByGroup($query, $group)
  {
    return $query->where('group', $group);
  }

  public function getGroupLabelAttribute()
  {
    return __('core::groups.' . $this->group);
  }
}

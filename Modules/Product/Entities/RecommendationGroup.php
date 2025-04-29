<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationGroup extends Model
{
  protected $fillable = ['show_in_home', 'show_in_filter'];
  const ALL_GROUPS_CACHE_kEY = 'allRecommendationGroups';

  protected static function booted()
  {
    static::updated(fn () => Cache::forget(self::ALL_GROUPS_CACHE_kEY));
  }

  public static function getAllGroups()
  {
    return Cache::rememberForever(self::ALL_GROUPS_CACHE_kEY, function () {
      return self::query()->withCount('items')->get();
    });
  }

  public static function getGroupsWithItemsForFront()
  {
    $recommendationGroups = self::getAllGroups()->where('show_in_home', 1)->filter(fn ($group) => $group->items_count > 0);
		foreach ($recommendationGroups as $group) {
			$productIds = Recommendation::where('group_id', $group->id)->latest('order')->pluck('product_id')->toArray();
			$products = Product::query()
				->select(['id', 'status', 'title', 'discount', 'discount_until', 'discount_type'])
				->whereIn('id', $productIds)
				->available(true)
				->take(8)
        ->orderByRaw('FIELD(`id`, ' . implode(", ", $productIds) . ')')
				->with([
					'media',
					'varieties' => function ($vQuery) {
						$vQuery->select(['id', 'product_id', 'discount', 'discount_until', 'discount_type', 'price']);
						$vQuery->with('store:id,variety_id,balance');
						$vQuery->with('product:id');
					}
				])
				->get()
				->each(function ($product) {
					$product->append(['main_image', 'final_price']);
					$product->makeHidden(['varieties', 'activeFlash']);
				});
			$group->products = $products;
		}
		return $recommendationGroups;
  }

  public function items()
  {
    return $this->hasMany(Recommendation::class, 'group_id'); 
  }

  public static function generateDefaultGroups()
  {
    $groups = [
      ['name' => 'newest', 'label' => 'جدید ترین', 'show_in_home' => 1, 'show_in_filter' => 1],
      ['name' => 'most_visited', 'label' => 'پربازدید ترین', 'show_in_home' => 1, 'show_in_filter' => 1],
      ['name' => 'low_to_high', 'label' => 'ارزان ترین', 'show_in_home' => 1, 'show_in_filter' => 1],
      ['name' => 'high_to_low', 'label' => 'گران ترین', 'show_in_home' => 1, 'show_in_filter' => 1],
      ['name' => 'top_sales', 'label' => 'پرفروش ترین', 'show_in_home' => 1, 'show_in_filter' => 1],
      ['name' => 'most_discount', 'label' => 'پرتخفیف ترین', 'show_in_home' => 1, 'show_in_filter' => 1],
    ];
    DB::table('recommendation_groups')->insert($groups);
    Cache::forget(self::ALL_GROUPS_CACHE_kEY);
  } 
}

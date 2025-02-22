<?php

namespace Modules\Product\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Classes\CoreSettings;
use Modules\Product\Entities\Product;

class NewProductService
{
  private $perPage;
  private $sortBy;

  public function __construct($perPage = null, $sortBy = null)
  {
    $this->sortBy = $sortBy ?? request('sort', 'newest');
    $this->perPage = $perPage ?? request('per_page', app(CoreSettings::class)->get('product.pagination', 12));
  }

  private static function getAvailableSortStatuses(): array
  {
    return ["'available'", "'soon'", "'out_of_stock'", "'draft'"];
  }

  public function getProducts()
  {
    $productsQuery = Product::query()->filters()->active();
    return $this->sort($productsQuery)->paginate($this->perPage)->withQueryString();
  }

  private function sort(Builder $query)
  {
    $sortStatus = self::getAvailableSortStatuses();
    $query->orderByRaw('FIELD(`status`, ' . implode(", ", $sortStatus) . ')');

    switch ($this->sortBy) {
      case 'newest':
        return $query->orderByDesc('id');
      case 'most_visited':
        return $query->orderByUniqueViews();
      case 'low_to_high':
      case 'high_to_low':
        return $this->orderByPrice($query);
      case 'top_sales':
        return $this->orderByTopSales($query);
      case 'most_discount':
        return $this->orderByMostDiscount($query);
      default:
        return $query;
    }
  }

  private function orderByPrice($query)
  {
    $activeProducts = $this->getActiveProductsData();
    $orderDirection = $this->sortBy === 'low_to_high' ? '' : 'DESC';

    $priceSortedProductIds = $activeProducts->sortBy(function ($product) {
      return $product->final_prices->min() ?? PHP_INT_MAX;
    })->pluck('id')->toArray();

    return $query->orderByRaw('FIELD(`id`, ' . implode(", ", $priceSortedProductIds) . ') ' . $orderDirection);
  }

  private function orderByTopSales($query)
  {
    $activeProducts = $this->getActiveProductsData();
    return $query->orderByRaw('FIELD(`id`, ' . implode(", ", $this->sortProductsBySales($activeProducts)) . ') DESC');
  }

  private function orderByMostDiscount($query)
  {
    $activeProducts = $this->getActiveProductsData();
    $discountedProductIds = $this->sortProductsByDiscount($activeProducts);
    return $query->available()->whereIn('id', $discountedProductIds)
      ->orderByRaw('FIELD(`id`, ' . implode(", ", $discountedProductIds) . ') DESC');
  }

  private function getActiveProductsData()
  {
    return Cache::remember('allActiveProductData', 6000, function () {
      return Product::query()
        ->with([
          'varieties' => function ($varietiesQuery) {
            $varietiesQuery->active()
              ->select(['id', 'product_id', 'price', 'discount', 'discount_type', 'deleted_at'])
              ->with(['product' => function ($productQuery) {
                $productQuery->select('id')->with('activeFlash');
              }]);
          },
          'orderItems' => function ($orderItemQuery) {
            $orderItemQuery->select(['id', 'status', 'product_id', 'quantity']);
          }
        ])
        ->select(['id', 'status'])
        ->active()
        ->get()
        ->map([$this, 'mapProductData']);
    });
  }

  private function mapProductData($product)
  {
    $product->total_sales = $product->orderItems->where('status', 1)->sum('quantity');
    $product->most_discount = $product->varieties->max('final_price.discount') ?: 0;

    $product->final_prices = $product->varieties->map(function ($variety) {
      return $variety->final_price['amount'];
    });

    return $product;
  }

  private function sortProductsBySales($activeProducts)
  {
    return $activeProducts->sortByDesc('total_sales')->pluck('id')->toArray();
  }

  private function sortProductsByDiscount($activeProducts)
  {
    return $activeProducts->filter(fn($item) => $item->most_discount > 0)
      ->pluck('id')
      ->sort()
      ->values()
      ->toArray();
  }
}

<?php

namespace Modules\Product\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Classes\CoreSettings;
use Modules\Product\Entities\Product;

class NewProductService
{
  private Builder $productQuery;

  public function __construct(private $perPage = null, private $sortBy = null)
  {
    $this->sortBy = $sortBy ?? request('sort', 'newest');
    $this->perPage = $perPage ?? request('per_page', app(CoreSettings::class)->get('product.pagination', 12));
    $this->productQuery = Product::query()->select(['id', 'status', 'title']);
  }

  private static function getAvailableSortStatuses(): array
  {
    return ["'available'", "'soon'", "'out_of_stock'", "'draft'"];
  }

  private static function getActiveProductsData()
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
        ->map(fn ($product) => self::mapProductData($product));
    });
  }

  private static function mapProductData($product)
  {
    $product->total_sales = $product->orderItems->where('status', 1)?->sum('quantity') ?? 0;
    $product->discount = $product->varieties->max(fn ($variety) => $variety->final_price['discount'] ?? 0);
    $product->final_price = $product->varieties->min(fn ($variety) => $variety->final_price['amount'] ?? 0);

    return $product;
  }

  public function getProducts()
  {
    $this->applyFilters();
    $this->loadRelations();
    $this->sort();

    return $this->productQuery->paginate($this->perPage)->withQueryString();
  }

  private function applyFilters()
  {
    $this->productQuery->filters()->active();
    if (request('min_price') || request('max_price')) {
      $productIdsFilteredByPrice = $this->sortProductsByPrice();
      $this->productQuery->whereIn('id', $productIdsFilteredByPrice);
    }
  }

  private function loadRelations()
  { 
    $this->productQuery->with([
      'varieties' => function ($varietiesQuery): void {
        $varietiesQuery->active()
          ->select(['id', 'product_id', 'price', 'purchase_price', 'discount', 'discount_type', 'max_number_purchases', 'deleted_at'])
          ->with([
            'product' => function ($productQuery): void {
              $productQuery->select('id')->with('activeFlash');
            },
            'store:id,variety_id,balance'
          ]);
      }
    ]);
  }

  private function sort()
  {
    $sortStatus = self::getAvailableSortStatuses();
    $this->productQuery->orderByRaw('FIELD(`status`, ' . implode(", ", $sortStatus) . ')');

    switch ($this->sortBy) {
      case 'newest':
        $this->productQuery->orderByDesc('id');
        break;
      case 'most_visited':
        $this->productQuery->orderByUniqueViews();
        break;
      case 'low_to_high':
      case 'high_to_low':
        $this->orderByPrice();
        break;
      case 'top_sales':
        $this->orderByTopSales();
        break;
      case 'most_discount':
        $this->orderByMostDiscount();
        break;
    }
  }

  private function orderByPrice()
  {
    $orderDirection = $this->sortBy === 'low_to_high' ? '' : 'DESC';
    $this->productQuery->orderByRaw('FIELD(`id`, ' . implode(", ", $this->sortProductsByPrice()) . ') ' . $orderDirection);
  }

  private function orderByTopSales()
  {
    $this->productQuery->orderByRaw('FIELD(`id`, ' . implode(", ", $this->sortProductsBySales()) . ') DESC');
  }

  private function orderByMostDiscount()
  {
    $this->productQuery->orderByRaw('FIELD(`id`, ' . implode(", ", $this->sortProductsByDiscount()) . ') DESC');
  }

  private function sortProductsBySales()
  {
    return self::getActiveProductsData()
      ->sortByDesc('total_sales')
      ->pluck('id')
      ->toArray();
  }

  private function sortProductsByDiscount()
  {
    return self::getActiveProductsData()
      ->filter(fn($item) => $item->discount > 0)
      ->pluck('id')
      ->sort()
      ->values()
      ->toArray();
  }

  private function sortProductsByPrice()
  {
    return self::getActiveProductsData()
      ->sortBy(fn ($product) => $product->final_price)
      ->when(request('min_price'), fn ($c) => $c->where('final_price', '>=', request('min_price')))
      ->when(request('max_price'), fn ($c) => $c->where('final_price', '<=', request('max_price')))
      ->pluck('id')
      ->toArray();
  }
}

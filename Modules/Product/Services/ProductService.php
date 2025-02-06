<?php

namespace Modules\Product\Services;

use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Shetabit\Shopit\Modules\Product\Services\ProductService as BaseProductService;
use Cache;
class ProductService extends BaseProductService
{

    public function addIdsInCache($sortByField, $with = false, $append = false, $cacheName = 'product')
    {
        return Cache::remember($cacheName, $this->cacheTime , function () use (&$sortByField, $with, $append) {
            $products = Product::query()
                ->select(['id', 'status'])
                ->active();
            if ($with) {
                $products->with($with);
            }

            $getProducts = $products->get();

            if ($append) {
                $getProducts->append($append);
            }

            if ($sortByField == 'price'){
                return array_values($getProducts->sortBy($sortByField)->map(function ($item){
                    return ['id' => $item->id, 'price' => $item->price];
                })->toArray());
            }

            if ($sortByField == 'most_discount') {
                // تخفیف دار ترین باید اونهایی که اصلا تخفیف ندارن و حساب نکنه
                $arr = array_values(
                    $getProducts
                        ->sortBy($sortByField) // Sort the products by the specified field
                        ->filter(fn($item) => $item->most_discount > 0) // Filter products where 'most_discount' is greater than 0
                        ->map(function ($item) {
                            return $item->id; // Map each product to its ID
                        })
                        ->toArray() // Convert the collection to an array
                );

// Sort the array by product IDs
                sort($arr);

                return $arr;

            }

            return array_values($getProducts->sortBy($sortByField)->pluck('id')->toArray());
        });
    }


    public function getProduct(): \Illuminate\Database\Eloquent\Builder
    {
        $attributeValue= $this->getRequest()->attribute_value;
        $attributeValueId = $this->getRequest()->attribute_value_id;
        $categoryId = $this->getRequest()->category_id;
        $available = $this->getRequest()->available;
        $flashId = $this->getRequest()->flash_id;
        $colorId = $this->getRequest()->color_id;
        $colorIds = $this->getRequest()->color_ids;
        $vip = $this->getRequest()->vip;
        $colorIds = ($colorIds && count($colorIds)) ? $colorIds : ($colorId ? [$colorId] : []);

        $products = Product::query()
            ->when($vip,function($query){
                $query->where('published_at','>',now());
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('categories', function($item) use ($categoryId){
                    $item->where('id', $categoryId)->orWhere('parent_id', $categoryId);
                });
            })->when($colorIds, function ($query) use ($colorIds) {
                $query->whereHas('varieties', function ($item) use ($colorIds) {
                    $item->whereIn('color_id', $colorIds);
                });
            })->when($flashId, function ($query) use ($flashId) {
                $query->whereHas('activeFlash', function ($item) use ($flashId) {
                    $item->where('flashes.id', $flashId);
                });
            })->when($attributeValueId || $attributeValue || request('color'), function ($query) use ($attributeValueId, $attributeValue) {
               $query->whereHas('varieties', function ($query) use ($attributeValueId, $attributeValue){
                   if (!empty($attributeValueId)){
                       $query->whereHas('attributes', function ($query2) use ($attributeValueId) {
                           if (request('attributes_by_value')) {
                               $query2->whereIn('attribute_variety.value', $attributeValueId);
                           } else {
                               $query2->whereIn('attribute_variety.attribute_value_id', $attributeValueId);
                           }
                       });
                   }
                   if (!empty($attributeValue)) {
                       foreach ($attributeValue as $value) {
                           $query->where('a_v.value', 'LIKE', '%'.$value.'%');
                       }
                   }
                   if (!empty(request('color'))) {
                       $query->whereHas('attributes', function ($query) use ($attributeValueId) {
                           $query->where(DB::raw('attribute_variety.value'), 'LIKE', '%'.request('color').'%');
                       });
                   }
               });
            })->with(['categories', 'unit', 'brand',
            'activeFlash', 'varieties.attributes','varietyOnlyDiscountsRelationship']);

        if ($available){
            $products->available();
        }else{
            $products->active();
        }

        return $products;
    }

    public function getRequest(): object
    {
        $sort = request('sort' , false);
        $title = request('title' , false);
        $colorId = request('color_id' , false);
        $colorIds = request('color_ids' , false);
        $flash_id = request('flash_id' , false);
        $minPrice = request('min_price' , request('minPrice'));
        $maxPrice = request('max_price' , request('maxPrice'));
        $available = request('available' , false);
        $vip = request('vip' , false);
        $category_id = request('category_id' , false);
        $attribute_value_id = request('attribute_value_id' , false);
        $attribute_value = request('attribute_value' , false);

        return (object) [
            'sort' => $sort,
            'title' => $title,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'flash_id' => $flash_id,
            'available' => $available,
            'category_id' => $category_id,
            'attribute_value_id' => $attribute_value_id,
            'attribute_value' => $attribute_value,
            'color_id' => $colorId,
            'color_ids' => $colorIds,
            'list' => request('list'),
            'vip' => $vip
        ];
    }



    public function sort($products, $sort)
    {
        $sortTypes = ['most_visited', 'low_to_high', 'high_to_low', 'top_sales',
            'low_sales', 'most_popular', 'most_discount', 'newest'];
        $orderByPriceIds = $this->getIdsPrice();
        $orderBySalesIds = $this->getCache('product_total_sales_ids');
        $orderByFavoriteIds = $this->getCache('product_total_favorite_ids');
        $orderByMostDiscountIds = $this->getCache('product_most_discount_ids');
        $sortStatus = [
            "'available'", "'soon'", "'out_of_stock'", "'draft'"
        ];

        $products = $products->orderByRaw('FIELD(`status`, '.implode(", " , $sortStatus).')');

        if ($sort === $sortTypes[0]){
            $products->orderByUniqueViews();
        }elseif ($sort === $sortTypes[1]){
            $products->orderByRaw('FIELD(`id`, '.implode(", " , $orderByPriceIds).')');
        }elseif ($sort === $sortTypes[2]) {
            $products->orderByRaw('FIELD(`id`, '.implode(", " , $orderByPriceIds).') DESC');
        }elseif ($sort === $sortTypes[3]){
            $products->orderByRaw('FIELD(`id`, '.implode(", " , $orderBySalesIds).') DESC');
        }elseif ($sort === $sortTypes[4]){
            $products->orderByRaw('FIELD(`id`, '.implode(", " , $orderBySalesIds).') ');
        }elseif ($sort === $sortTypes[5]){
            $products->orderByRaw('FIELD(`id`, '.implode(", " , $orderByFavoriteIds).') DESC');
        }elseif ($sort === $sortTypes[6]) {
            $products->orderByRaw('FIELD(`id`, '.implode(", " , $orderByMostDiscountIds).') DESC')->available()->whereIn('id', $orderByMostDiscountIds);
        }else{
            $products->orderBy('created_at','DESC'); // newest
        }

        return $products;
    }
}

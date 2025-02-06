<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Category\Entities\Category;

class SpecificDiscountItem extends Model
{
    protected $fillable = [
        'specific_discount_type_id',
        'type',
        'model_ids',
        'balance',
        'balance_type',
        'range_from',
        'range_to',
    ];
    const TYPE_CATEGORY = 'category';
    const TYPE_PRODUCT = 'product';
    const TYPE_BALANCE = 'balance';
    const TYPE_RANGE = 'range';

    const BALANCE_TYPE_MORE = 'more';
    const BALANCE_TYPE_LESS = 'less';


    public function specific_discount_type()
    {
        return $this->belongsTo(SpecificDiscountType::class, 'specific_discount_type_id');
    }

    public static function getAvailableTypes()
    {
        return [
            self::TYPE_CATEGORY,
            self::TYPE_PRODUCT,
            self::TYPE_BALANCE,
            self::TYPE_RANGE
        ];
    }

    public function getTypeLabelAttribute()
    {
        $arr = [
            self::TYPE_CATEGORY => 'دسته بندی ها',
            self::TYPE_PRODUCT => 'محصولات',
            self::TYPE_BALANCE => 'موجودی',
            self::TYPE_RANGE => 'بازه قیمت',
        ];

        return $arr[$this->type];
    }

    public function apply_discounts(): void
    {
        $specificDiscountType = $this->specific_discount_type;
        $specificDiscount = $this->specific_discount_type->specific_discount;
        switch ($this->type) {
            case SpecificDiscountItem::TYPE_CATEGORY: /* TYPE_CATEGORY */
                // get categories
                $model_ids = explode(',', $this->model_ids);
                $categories = Category::query()->whereIn('id', $model_ids)->get();
                foreach ($categories as $category) {
                    $products = $category->products;
                    foreach ($products as $product) {
                        $product->discount = $specificDiscountType->discount;
                        $product->discount_type = $specificDiscountType->discount_type;
                        $product->discount_until = $specificDiscount->end_date;
                        $product->save();
                    }
                }
                break;
            case SpecificDiscountItem::TYPE_PRODUCT: /* TYPE_PRODUCT */
                $model_ids = explode(',', $this->model_ids);
                $products = Product::query()->whereIn('id', $model_ids)->get();
                foreach ($products as $product) {
                    $product->discount = $specificDiscountType->discount;
                    $product->discount_type = $specificDiscountType->discount_type;
                    $product->discount_until = $specificDiscount->end_date;
                    $product->save();
                }
                break;
            case SpecificDiscountItem::TYPE_BALANCE: /* TYPE_BALANCE */
                $products = Product::select('products.*', DB::raw('SUM(stores.balance) as total_balance'))
                    ->withCommonRelations()
                    ->join('varieties', 'products.id', '=', 'varieties.product_id')
                    ->join('stores', 'varieties.id', '=', 'stores.variety_id')
                    ->groupBy('products.id')
                    ->having('total_balance', ($this->balance_type == SpecificDiscountItem::BALANCE_TYPE_MORE) ? '>=' : '<=', $this->balance)
                    ->orderByDesc('total_balance')
                    ->get();
                foreach ($products as $product) {
                    $product->discount = $specificDiscountType->discount;
                    $product->discount_type = $specificDiscountType->discount_type;
                    $product->discount_until = $specificDiscount->end_date;
                    $product->save();
                }
                break;
            case SpecificDiscountItem::TYPE_RANGE: /* TYPE_RANGE */
                $products = Product::query()->whereBetween('unit_price', [$this->range_from, $this->range_to])->get();
                foreach ($products as $product) {
                    $product->discount = $specificDiscountType->discount;
                    $product->discount_type = $specificDiscountType->discount_type;
                    $product->discount_until = $specificDiscount->end_date;
                    $product->save();
                }
                break;
        }
    }



//    public function disable_discounts($type, $specificDiscount): void
//    {
//        switch ($type) {
//            case SpecificDiscountItem::TYPE_CATEGORY: /* TYPE_CATEGORY */
//                // get categories
//                $model_ids = explode(',', $specificDiscount->model_ids);
//                $categories = Category::find($model_ids);
//                foreach ($categories as $category) {
//                    $products = $category->products;
//                    foreach ($products as $product) {
//                        $product->discount = null;
//                        $product->discount_type = null;
//                        $product->discount_until = null;
//                        $product->save();
//                    }
//                }
//                break;
//            case SpecificDiscountItem::TYPE_PRODUCT: /* TYPE_PRODUCT */
//                $model_ids = explode(',', $specificDiscount->model_ids);
//                $products = Product::find($model_ids);
//                foreach ($products as $product) {
//                    $product->discount = null;
//                    $product->discount_type = null;
//                    $product->discount_until = null;
//                    $product->save();
//                }
//                break;
//            case SpecificDiscountItem::TYPE_BALANCE: /* TYPE_BALANCE */
//                $products = Product::select('products.*', DB::raw('SUM(stores.balance) as total_balance'))
//                    ->withCommonRelations()
//                    ->join('varieties', 'products.id', '=', 'varieties.product_id')
//                    ->join('stores', 'varieties.id', '=', 'stores.variety_id')
//                    ->groupBy('products.id')
//                    ->having('total_balance', ($specificDiscount->balance_type == SpecificDiscountItem::BALANCE_TYPE_MORE) ? '>=' : '<=', $specificDiscount->balance)
//                    ->orderByDesc('total_balance')
//                    ->get();
//                foreach ($products as $product) {
//                    $product->discount = null;
//                    $product->discount_type = null;
//                    $product->discount_until = null;
//                    $product->save();
//                }
//                break;
//            case SpecificDiscountItem::TYPE_RANGE: /* TYPE_RANGE */
//                $products = Product::query()->whereBetween('sell_price', [$specificDiscount->range_from, $specificDiscount->range_to])->get();
//                foreach ($products as $product) {
//                    $product->discount = null;
//                    $product->discount_type = null;
//                    $product->discount_until = null;
//                    $product->save();
//                }
//                break;
//        }
//    }


}

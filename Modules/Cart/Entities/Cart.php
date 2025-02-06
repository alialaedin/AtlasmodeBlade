<?php

namespace Modules\Cart\Entities;

use Modules\Product\Entities\Variety;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Shetabit\Shopit\Modules\Cart\Database\factories\CartFactory;
use Modules\Core\Entities\BaseModel;
use Modules\Customer\Entities\Customer;

/**
 * توجه
 * اگر داری دلیتینگ مینویسی
 * @see CartFromRequest@addToCartFromRequest
 */
class Cart extends BaseModel
{
  use HasFactory;

  protected $fillable = [
    'quantity',
    'variety_id',
  ];

  protected $appends = [];

  protected static $commonRelations = [
    'variety.product.unit',
    'variety.product.varieties.attributes',
    'variety.color',
    'customer',
    'variety.attributes.pivot.attributeValue'
  ];

  protected static function newFactory()
  {
    return CartFactory::new();
  }


  /**
   * Relations Function
   */
  public function variety(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Variety::class);
  }

  public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Customer::class);
  }


  public function scopeOwner($query)
  {
    $query->where('customer_id', auth()->user()->id);
  }

  public function setDiscountPrice($variety)
  {
    $this->attributes['discount_price'] = $variety->final_price['discount_price'];
  }

  public function setPrice($variety)
  {
    $this->attributes['price'] = $variety->final_price['amount'];
  }

  public static function addToCart($quantity, Variety $variety, Customer $customer)
  {
    $cart = new Cart([
      'quantity' => $quantity,
      'variety_id' => $variety->id
    ]);
    #set variety in CartStoreRequest
    $cart->setDiscountPrice($variety);
    $cart->setPrice($variety);
    $cart->customer()->associate($customer);
    $cart->variety()->associate($variety);
    $cart->save();
    $cart->loadCommonRelations();

    return $cart;
  }

  public static function fakeCartMakerWithOrderItems($orderItems)
  {
    $fakeCarts = [];
    foreach ($orderItems as $orderItem) {
      $newFakeCart = new Cart([
        'variety_id' => $orderItem->variety_id,
        'quantity' => $orderItem->quantity,
        'discount_price' => $orderItem->discount_amount,
        'price' => $orderItem->amount,
      ]);
      $newFakeCart->load(['variety' => function ($query) {
        $query->with('product');
      }]);
      $fakeCarts[] = $newFakeCart;
    }

    return collect($fakeCarts);
  }

  public static function fakeCartMaker($variety_id, $quantity, $discount_price, $price): Cart
  {
    return new Cart([
      'variety_id' => $variety_id,
      'quantity' => $quantity,
      'discount_price' => $discount_price,
      'price' => $price,
    ]);
  }

  public static function hasFreeShippingProduct($carts): bool
  {
    $varietyIds = $carts->pluck('variety_id')->toArray();
    $varieties = Variety::query()
      ->select(['id', 'product_id'])
      ->whereIn('id', $varietyIds)
      ->with('product')
      ->get();

    foreach ($varieties as $variety) {
      if ($variety->product->free_shipping) return true;
    }
    return false;
  }
}

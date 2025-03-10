<?php

namespace Modules\Cart\Entities;

use Modules\Product\Entities\Variety;
use Illuminate\Database\Eloquent\Model;
use Modules\Customer\Entities\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Cart extends Model
{
  protected $fillable = ['quantity', 'variety_id'];

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
  
  public static function addOrUpdateQuantity(Variety $variety, $quantity): self
  {
    $customer = Auth::guard('customer')->user();
    $cart = self::where('variety_id', $variety->id)->owner()->first();
    if (!$cart) {
      $cart = self::addToCart($quantity, $variety, $customer);
    }else {
      $cart->quantity += $quantity;
      $cart->save();
    }

    return $cart;
  }

  public function loadNecessaryRelations()
  { 
    $this->load([
      'variety'
    ]);
  }

  public function variety(): BelongsTo
  {
    return $this->belongsTo(Variety::class);
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class);
  }
}

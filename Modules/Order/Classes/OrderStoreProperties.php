<?php

namespace Modules\Order\Classes;

use Illuminate\Database\Eloquent\Collection;
use Modules\Cart\Entities\Cart;
use Modules\Coupon\Entities\Coupon;
use Modules\Customer\Entities\Address;
use Modules\Shipping\Entities\Shipping;

class OrderStoreProperties
{
  public Shipping $shipping;
  public Address $address;
  public ?Coupon $coupon = null;
  public Cart|array|Collection $carts;
  public string|int $discount_amount;
  public string|int $shipping_amount;
  public string|int $shipping_packet_amount;
  public string|int $discountOnOrder = 0;
  public string|int $discountOnCoupon = 0;
  public string|int $discountAmount;
  public string|int $discountOnItems;
  public string|int $totalAmount;
  public string|int $totalItemsAmount;
  public string|int $totalItemsAmountWithoutDiscount;
  public string|int $itemsCount;
  public string|int $itemsQuantity;
}

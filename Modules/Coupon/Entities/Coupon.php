<?php

namespace Modules\Coupon\Entities;

use Modules\Cart\Entities\Cart;
use Modules\Core\Helpers\Helpers;
use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\HasAuthors;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\BaseModel;
use Modules\Customer\Entities\Customer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Classes\CoreSettings;

class Coupon extends BaseModel
{
  use HasAuthors, SoftDeletes;

  protected $fillable = [
    'title',
    'code',
    'start_date',
    'end_date',
    'type',
    'amount',
    'usage_limit',
    'usage_per_user_limit',
    'min_order_amount'
  ];

  protected static array $commonRelations = ['customers'];

  const DISCOUNT_TYPE_FLAT = 'flat';
  const DISCOUNT_TYPE_PERCENTAGE = 'percentage';

  public function customers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
    return $this->belongsToMany(Customer::class)->withTimestamps()->withPivot(['amount']);
  }

  public static function getAvailableTypes(): array
  {
    return [static::DISCOUNT_TYPE_FLAT, static::DISCOUNT_TYPE_PERCENTAGE];
  }

  public function countCouponUsed(int|Coupon $coupon): int
  {
    if ($coupon instanceof Coupon) {
      return $coupon->customers()->count();
    }

    return DB::table('coupon_customer')->where('coupon_id', $coupon)->count();
  }

  public function countCouponUsedByCustomer(int|Customer $customer, int $couponId): int
  {
    if ($customer instanceof Customer) {
      return $customer->coupons()->where('coupon_id', $couponId)->count();
    }

    return DB::table('coupon_customer')->where('customer_id', $customer)
      ->where('coupon_id', $couponId)->count();
  }

  public function getTotalUsageAttribute()
  {
    if ($this->customers_count === null) {
      return $this->customers()->count();
    }

    return $this->customers_count;
  }

  public static function useCoupon($customerId, $couponId)
  {
    DB::table('coupon_customer')->insert([
      'customer_id' => $customerId,
      'coupon_id' => $couponId,
      'created_at' => now(),
      'updated_at' => now()
    ]);
  }

  public static function dontAllowCouponAndDiscountTogether()
  {
    $coreSettings = app(CoreSettings::class);
    if ($coreSettings->get('order.allow_coupon_with_discount')) {
      return;
    } else if (!$coreSettings->get('order.allow_coupon_mixed')) {
      static::allow_coupon_with_discount();
    } else {
      static::allow_coupon_mixed();
    }
  }

  // اجازه میکس نده و اجازه همراه با تخفیف نده
  public static function allow_coupon_with_discount()
  {
    /** @var Customer $customer */
    if ($customer = Auth::user()) {
      $hasDiscount = false;
      /** @var Cart $cart */
      foreach (
        $customer->carts()->with([
          'variety' => fn($q) => $q->withCommonRelations()
        ])->get() as $cart
      ) {
        if ($cart->variety->final_price['discount_price']) {
          $hasDiscount = true;
        }
      }

      if ($hasDiscount) {
        throw Helpers::makeValidationException('به علت وجود محصول در جشنوار امکان استفاده از کد تخفیف وجود ندارد');
      }
    }
  }

  // اونایی که تو جشنواره ان تخفیف اعمال نشه - باید حداقل یک محصول بدون تخفیف باشه
  public static function allow_coupon_mixed()
  {
    /** @var Customer $customer */
    if ($customer = Auth::user()) {
      $hasAnyNoDiscount = false;
      /** @var Cart $cart */
      foreach (
        $customer->carts()->with([
          'variety' => fn($q) => $q->withCommonRelations()
        ])->get() as $cart
      ) {
        if (!$cart->variety->final_price['discount_price']) {
          $hasAnyNoDiscount = true;
        }
      }

      if (!$hasAnyNoDiscount) {
        throw Helpers::makeValidationException('به علت وجود تمامی محصولات در جشنواره امکان استفاده از کد تخفیف وجود ندارد');
      }
    }
  }

  public function scopeFilters($query)
  {
    return $query
      ->when(request('title'), fn($q) => $q->where('title', 'LIKE', '%' . request('title') . '%')->orWhere('code', 'LIKE', '%' . request('title') . '%'))
      ->when(request('start_date'), fn($q) => $q->whereDate('start_date', '>=', request('start_date')))
      ->when(request('end_date'), fn($q) => $q->whereDate('end_date', '<=', request('end_date')));
  }
}

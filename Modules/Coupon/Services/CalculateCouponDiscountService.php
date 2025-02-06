<?php

namespace Modules\Coupon\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Entities\Cart;
use Modules\Core\Helpers\Helpers;
use Modules\Coupon\Entities\Coupon;
use Modules\Customer\Entities\Customer;
use Modules\Core\Classes\CoreSettings;

class CalculateCouponDiscountService
{

    protected ?\Illuminate\Contracts\Auth\Authenticatable $user;
    protected Coupon $model;

    /**
     * CalculateCouponDiscountService constructor.
     * @param $code
     * @param $totalPrice
     */
    public function __construct(protected $code,
                                protected $totalPrice,
    ){
        $this->user = auth()->user();
        $this->model = new Coupon();
    }

    /**
     * @return mixed
     */
    public function calculate()
    {
        $coupon = $this->model->where('code' , $this->code)->firstOrFail();
        $this->validation($coupon, $this->model, $this->user);

        return $this->calculationDiscount($coupon , $this->totalPrice);
    }

    /**
     * @param $coupon
     * @param $totalPrice
     * @return mixed
     */
    public function calculationDiscount($coupon , $totalPrice): mixed
    {
        $type = $coupon->type; // نوع تخفیف عددی یا درصدی
        $amount = $coupon->amount; // مبلغ یا درصد تخفیف

        $coreSettings = app(CoreSettings::class);
        if (!$coreSettings->get('order.allow_coupon_with_discount') && $coreSettings->get('order.allow_coupon_mixed')) {
            /** @var Customer $customer */
            if ($customer = Auth::user()) {
                $totalPrice = 0;
                /** @var Cart $cart */
                foreach ($customer->carts()->with([
                    'variety' => fn($q) => $q->withCommonRelations()
                ])->get() as $cart
                ) {
                    if (!$cart->variety->final_price['discount_price']) {
                        $totalPrice += $cart->variety->final_price['amount'] * $cart->quantity;
                    }
                }
            }
        }

        if ($type == $this->model::DISCOUNT_TYPE_FLAT){
            return $this->returnFormat($amount, 'flat');
        }
        $discount = (int)round(($amount * $totalPrice) / 100);
        #if type == static::DISCOUNT_TYPE_PERCENTAGE
        return $this->returnFormat($discount, 'percentage', $amount.'%');
    }

    public function returnFormat($amount, $type, $percentage = null)
    {
        return [
            'discount' => $amount,
            'type' => $type,
            'percentage' => $percentage,
        ];
    }


    /**
     * @param $coupon
     * @param $model
     * @param $user
     */
    public function validation($coupon, $model, $user)
    {
        $now = Carbon::now()->toDateString();
        $start_date = Carbon::parse($coupon->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($coupon->end_date)->format('Y-m-d');

        if (!(($start_date <= $now) && ($now <= $end_date))){
            throw Helpers::makeValidationException('تاریخ استفاده از کد تخفیف به پایان رسیده است.');
        }
        if ($coupon->usage_limit <= $model->countCouponUsed($coupon->id)){
            throw Helpers::makeValidationException('تعداد استفاده از این کد تخفیف به اتمام رسیده است');
        }
        if ($coupon->usage_per_user_limit <= $model->countCouponUsedByCustomer($user , $coupon->id)){
            throw Helpers::makeValidationException('تعداد استفاده شما از این کد تخفیف به اتمام رسیده است');
        }
        if ($coupon->min_order_amount !== null && ($this->totalPrice < $coupon->min_order_amount)) {
            throw Helpers::makeValidationException('حداقل مبلغ سبد خرید برای استفاده از این کد تخفیف '
                . $coupon->min_order_amount . ' تومان است');
        }
    }
}

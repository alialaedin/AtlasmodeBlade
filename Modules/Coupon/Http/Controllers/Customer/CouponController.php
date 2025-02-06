<?php

namespace Modules\Coupon\Http\Controllers\Customer;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Cart\Entities\Cart;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Http\Requests\Customer\CouponVerifyRequest;
use Modules\Coupon\Services\CalculateCouponDiscountService;
use Modules\Customer\Entities\Customer;

class CouponController extends Controller
{
  /**
   * @param CouponVerifyRequest $request
   * @return JsonResponse
   */
  public function verify(CouponVerifyRequest $request): JsonResponse
  {
    $code = $request->code;
    $totalPrice = $request->total_price;

    $discount =  (new CalculateCouponDiscountService($code, $totalPrice))->calculate();

    return response()->success('تخفیف شما', compact('discount'));
  }
}

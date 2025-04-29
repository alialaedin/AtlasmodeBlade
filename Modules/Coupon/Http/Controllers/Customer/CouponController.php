<?php

namespace Modules\Coupon\Http\Controllers\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Coupon\Http\Requests\Customer\CouponVerifyRequest;
use Modules\Coupon\Services\CalculateCouponDiscountService;

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

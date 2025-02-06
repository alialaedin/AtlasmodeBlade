<?php

namespace Modules\Coupon\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Core\Helpers\Helpers;
use Modules\Coupon\Entities\Coupon;
use Modules\Product\Services\Validations\DiscountValidationService;

class CouponUpdateRequest extends FormRequest
{
  public function rules()
  {
    $code = Helpers::getModelIdOnPut('coupon');
    return [
      "title" => 'required|string',
      "code" => 'required|string|unique:coupons,code,' . $code,
      "start_date" => 'required|date_format:Y-m-d H:i',
      "end_date" => 'required|after_or_equal:start_date',
      "type" => ['required', Rule::in(Coupon::getAvailableTypes())],
      "amount" => 'required|integer',
      "usage_limit" => 'nullable|integer|min:1',
      "usage_per_user_limit" => 'nullable|integer|min:1',
      'min_order_amount' => 'nullable|integer|min:1000'
    ];
  }

  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation()
  {
    function removeComma($amount): int
    {
      return (int) str_replace(',', '', $amount);
    }

    $this->merge([
      'amount' => removeComma($this->input('amount')),
      'min_order_amount' => removeComma($this->input('min_order_amount'))
    ]);

  }

  protected function passedValidation()
  {
    (new DiscountValidationService($this->type, new Coupon, $this->amount, null))->checkDiscount();
  }
}

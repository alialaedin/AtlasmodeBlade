<?php

namespace Modules\Coupon\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Coupon\Entities\Coupon;
use Modules\Product\Services\Validations\DiscountValidationService;

class CouponStoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      "title" => 'required|string',
      "code" => 'required|string|unique:coupons',
      "start_date" => 'required|date_format:Y-m-d H:i|after_or_equal:' . Carbon::now()->subMinute(),
      "end_date" => 'required|date_format:Y-m-d H:i|after_or_equal:start_date',
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

  protected function prepareForValidation(): void
  {
    function removeComma($amount): int
    {
      return (int) str_replace(',', '', $amount);
    }

    $this->merge([
      'amount' => removeComma($this->input('amount')),
      'min_order_amount' => $this->filled('min_order_amount') ? removeComma($this->input('min_order_amount')) : null
    ]);

  }

  protected function passedValidation()
  {
    (new DiscountValidationService($this->type, new Coupon, $this->amount, null))->checkDiscount();
  }
}

<?php

namespace Modules\Auth\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Modules\Core\Rules\IranMobile;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\SmsToken;

class CustomerLoginRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'mobile' => ['required', 'digits:11', new IranMobile()],
      'password' => [
        'required',
        Password::min(6)
      ]
    ];
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * @throws ValidationException
   */
  public function passedValidation()
  {
    $customer = Customer::where('mobile', $this->mobile)->first();
    if (! $customer) {
      throw ValidationException::withMessages([
        'mobile' => ['کاربری پیدا نشد!']
      ]);
    }

    //Check SMS token
    $smsToken = SmsToken::where('mobile', $this->mobile)->first();
    if (! $smsToken) {
      throw ValidationException::withMessages([
        'mobile' => ['کاربری با این مشخصات پیدا نشد!']
      ]);
    } elseif (! $smsToken->verified_at) {
      throw ValidationException::withMessages([
        'mobile' => ['شماره موبایل کاربر احراز نشده است.']
      ]);
    }

    $this->merge([
      'customer' => $customer
    ]);
  }
}

<?php

namespace Modules\Auth\Http\Requests\Customer;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Core\Rules\IranMobile;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\SmsToken;

class CustomerVerifyRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'mobile' => ['required', 'digits:11', new IranMobile()],
      'sms_token' => 'required',
      // 'type' => ['required', Rule::in(['register', 'forget', 'login'])]
    ];
  }

  public function authorize(): bool
  {
    return true;
  }

  /**
   * @throws ValidationException
   */
  public function passedValidation()
  {
    $smsToken = SmsToken::where('mobile', $this->mobile)->first();

    if (!$smsToken) {
      throw ValidationException::withMessages([
        'mobile' => ['کاربری با این مشخصات پیدا نشد!']
      ]);
    } elseif ($smsToken->token !== $this->sms_token) {
      throw ValidationException::withMessages([
        'sms_token' => ['کد وارد شده نادرست است!']
      ]);
    } elseif (Carbon::now()->gt($smsToken->expired_at)) {
      throw ValidationException::withMessages([
        'sms_token' => ['کد وارد شده منقضی شده است!']
      ]);
    }

    // if ($this->type !== 'register') {
    //   $customer = Customer::where('mobile', $this->mobile)->first();
    // }


    $this->merge([
      'smsToken' => $smsToken,
      // 'customer' => $customer ?? null
    ]);
  }
}

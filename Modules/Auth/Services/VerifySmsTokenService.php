<?php

namespace Modules\Auth\Services;

use Carbon\Carbon;
use Modules\Customer\Entities\SmsToken;
use Illuminate\Validation\ValidationException;

class VerifySmsTokenService
{
  /**
   * @throws ValidationException
   */
  public function verify(string $mobile, string $token): bool
  {
    $smsToken = SmsToken::where('mobile', $mobile)->first();

    if (!$smsToken) {
      throw ValidationException::withMessages([
        'mobile' => ['کاربری با این مشخصات پیدا نشد!']
      ]);
    } elseif ($smsToken->token !== $token) {
      throw ValidationException::withMessages([
        'sms_token' => ['کد وارد شده نادرست است!']
      ]);
    } elseif (Carbon::now()->diffInMinutes($smsToken->updated_at) > 5) {
      throw ValidationException::withMessages([
        'sms_token' => ['کد وارد شده منقضی شده است!']
      ]);
    }
  }
}

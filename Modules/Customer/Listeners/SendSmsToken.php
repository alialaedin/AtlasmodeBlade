<?php

namespace Modules\Customer\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Core\Classes\CoreSettings;
use Shetabit\Shopit\Modules\Sms\Sms;
use Modules\Core\Helpers\Helpers;
use Modules\Customer\Entities\SmsToken;
use Modules\Customer\Events\SmsVerify;

class SendSmsToken
{
  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Handle the event.
   *
   * @param SmsVerify $event
   * @return Sms
   */
  public function handle(SmsVerify $event): Sms|array
  {
    $pattern = app(CoreSettings::class)->get('sms.patterns.verification_code');
    $digits = app(CoreSettings::class)->get('sms.digits', 5);
    // $token = Helpers::randomNumbersCode($digits);
    $token = 12345;
    $mobile = $event->mobile;

    $output['status'] = 200;
    // $output = Sms::pattern($pattern)->data([
    //   'code' => $token
    // ])->to([$mobile])->send();

    if ($output['status'] != 200) {
      Log::debug('', [$output]);
    }

    if ($output['status'] == 200) {
      //store into database
      $smsToken = SmsToken::where('mobile', $event->mobile)->first();
      if ($smsToken) {
        $smsToken->update([
          'token' => $token,
          'expired_at' => Carbon::now()->addHours(24)
        ]);
      } else {
        SmsToken::query()->create([
          'mobile' => $event->mobile,
          'token' => $token,
          'expired_at' => Carbon::now()->addHours(24)
        ]);
      }
    }

    return $output;
  }
}

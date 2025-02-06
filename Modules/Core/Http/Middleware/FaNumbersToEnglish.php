<?php

namespace Modules\Core\Http\Middleware;

use Illuminate\Http\Request;
use Modules\Core\Helpers\Helpers;

class FaNumbersToEnglish
{
  public static $keys = [
    'mobile',
    'postal_code',
    'password',
    'sms_token'
  ];
  public function handle(Request $request, $next)
  {
    foreach (static::$keys as $key) {
      if ($request->filled($key)) {
        $request->merge([
          $key => Helpers::convertFaNumbersToEn($request->input($key))
        ]);
      }
    }

    return $next($request);
  }
}

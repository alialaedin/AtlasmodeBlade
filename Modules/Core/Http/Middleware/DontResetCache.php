<?php

namespace Modules\Core\Http\Middleware;

use Illuminate\Http\Request;

class DontResetCache
{
  /**
   * @param Request $request
   * @param $next
   */
  public function handle($request, $next)
  {
    $request->attributes->set('do_not_reset_cache', true);


    return $next($request);
  }
}

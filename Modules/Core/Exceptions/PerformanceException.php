<?php

namespace Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\Request;

class PerformanceException extends Exception
{
  /**
   * Report the exception.
   *
   * @return void
   */
  public function report()
  {
    //
  }

  /**
   * Render the exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function render(Request $request)
  {
    $code = $this->getCode();
    if ($code == 0) {
      $code = 409;
    }

    return response()->error('Performance error: ' . $this->getMessage(), [], $code);
  }
}

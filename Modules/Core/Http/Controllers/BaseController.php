<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

abstract class BaseController extends Controller
{
  public function run($job, $params)
  {
    return app()->make($job, $params)->handle();
  }
}

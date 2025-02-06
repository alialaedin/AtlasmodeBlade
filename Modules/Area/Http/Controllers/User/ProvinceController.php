<?php

namespace Modules\Area\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Modules\Area\Entities\Province;

class ProvinceController extends Controller
{
  public function index()
  {
    $provinces = Province::withCommonRelations()->get();

    return response()->success('', compact('provinces'));
  }


  public function show(Int $id)
  {
    $province = Province::withCommonRelations()->findOrFail($id);

    return response()->success('', compact('province'));
  }
}

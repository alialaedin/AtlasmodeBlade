<?php

namespace Modules\Area\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Modules\Area\Entities\City;

class CityController extends Controller
{
  public function index()
  {
    $cities = City::withCommonRelations()->get();

    return response()->success('', compact('cities'));
  }


  public function show(Int $id)
  {
    $city = City::withCommonRelations()->findOrFail($id);

    return response()->success('', compact('city'));
  }
}

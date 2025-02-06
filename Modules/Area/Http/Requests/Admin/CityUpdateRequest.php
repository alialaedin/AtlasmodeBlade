<?php

namespace Modules\Area\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;

class CityUpdateRequest extends FormRequest
{
  public function rules()
  {
    $cityId = Helpers::getModelIdOnPut('city');

    return  [
      'name' => ["required", "string", "unique:cities,id," . $cityId],
      'province_id' => ["required", "exists:provinces,id"],
      'status' => ["nullable", "boolean"],
    ];
  }

  public function prepareForValidation()
  {
    $this->merge([
      'status' => $this->has('status') && $this->filled('status') ? 1 : 0
    ]);
  }  
}

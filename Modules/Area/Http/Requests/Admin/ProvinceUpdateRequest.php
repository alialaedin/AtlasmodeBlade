<?php

namespace Modules\Area\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;

class ProvinceUpdateRequest extends FormRequest
{
  public function rules()
  {
    $provinceId = Helpers::getModelIdOnPut('province');

    return  [
      'name' => ["required", "string", "unique:provinces,id," . $provinceId],
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

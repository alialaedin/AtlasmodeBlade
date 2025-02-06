<?php

namespace Modules\Area\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProvinceStoreRequest extends FormRequest
{
  public function rules()
  {
    return  [
      'name' => ["required", "string", "unique:provinces"],
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

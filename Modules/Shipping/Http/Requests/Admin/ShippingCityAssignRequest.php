<?php

namespace Modules\Shipping\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingCityAssignRequest extends FormRequest
{
  public function rules()
  {
    return [
      'cities' => 'nullable|array',
      'cities.*.id' => 'bail|integer|exists:cities,id',
      'cities.*.price' => 'bail|nullable|integer',
    ];
  }
}

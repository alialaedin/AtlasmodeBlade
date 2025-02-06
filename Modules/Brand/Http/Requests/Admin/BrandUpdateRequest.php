<?php

namespace Modules\Brand\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;

class BrandUpdateRequest extends FormRequest
{
  public function rules(): array
  {
    $brandId = Helpers::getModelIdOnPut('brand');

    return [
      'name' => 'required|string|unique:brands,name,' . $brandId,
      'status' => 'nullable|boolean',
      'show_index' => 'nullable|boolean',
      'description' => 'nullable|string',
      'image' => 'nullable|file|mimes:jpg,jpeg,png'
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
  
  public function prepareForValidation(): void
  {
    $this->merge([
      'status' => $this->status ? 1 : 0,
      'show_index' => $this->show_index ? 1 : 0
    ]);
  } 
}

<?php

namespace Modules\Brand\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandStoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => 'required|string|unique:brands,name',
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

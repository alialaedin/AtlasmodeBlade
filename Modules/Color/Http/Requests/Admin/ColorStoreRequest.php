<?php

namespace Modules\Color\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Rules\ColorCode;
//use Shetabit\Shopit\Modules\Color\Http\Requests\Admin\ColorStoreRequest as BaseColorStoreRequest;

class ColorStoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:100|unique:colors',
      'code' => ['required', 'string', 'max:100', 'unique:colors', new ColorCode()],
      'status' => 'required|boolean'
    ];
  }
  
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    $this->merge([
      'status' => $this->status ? 1 : 0
    ]);
  }
}

<?php

namespace Modules\Color\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Core\Rules\ColorCode;

class ColorUpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => [
        'required',
        'string',
        'max:100',
        Rule::unique('colors')->ignore($this->route('color'))
      ],
      'code' => [
        'required',
        'string',
        'max:100',
        Rule::unique('colors')->ignore($this->route('color')),
        new ColorCode()
      ],
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

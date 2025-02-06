<?php

namespace Modules\Unit\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Unit\Entities\Unit;

class UnitStoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:191|unique:units',
      'symbol' => 'required|string|max:191',
      'precision' => ['required', 'numeric', Rule::in(Unit::PRECISION)],
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

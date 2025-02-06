<?php

namespace Modules\Unit\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Unit\Entities\Unit;

class UnitUpdateRequest extends FormRequest
{
  public function rules()
  {
    return [
      'name' => ['required', 'string', 'max:191', Rule::unique('units')->ignore($this->route('unit'))],
      'symbol' => 'required|string|max:191',
      'precision' => ['required', 'numeric', Rule::in(Unit::PRECISION)],
      'status' => 'required|boolean'
    ];
  }

  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'status' => $this->status ? 1 : 0
    ]);
  }
}

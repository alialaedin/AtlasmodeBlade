<?php

namespace Modules\Specification\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Specification\Entities\Specification;

class SpecificationStoreRequest extends FormRequest
{
  public function rules()
  {
    $rule = Rule::requiredIf(in_array($this->type, [Specification::TYPE_SELECT, Specification::TYPE_MULTI_SELECT]));

    return [
      'name' => 'required|string|max:191|unique:specifications',
      'label' => 'required|string|max:191',
      'type' => ['required', 'string', 'max:50', Rule::in(Specification::getAvailableTypes())],
      'group' => 'nullable|string|max:191',
      'show_filter' => 'required|boolean',
      'public' => 'required|boolean',
      'status' => 'required|boolean',
      'required' => 'required|boolean',
      'values' => [$rule, "array"],
      'values.*' => [$rule, 'string', 'max:191']
    ];
  }

  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'show_filter' => $this->input('show_filter') ? 1 : 0,
      'status' => $this->input('status') ? 1 : 0,
      'public' => $this->input('public') ? 1 : 0,
      'group' => $this->group ?: 'public',
      'required' => $this->input('required') ? 1 : 0,
    ]);
  }
}

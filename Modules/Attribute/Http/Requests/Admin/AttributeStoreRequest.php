<?php

namespace Modules\Attribute\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeStoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:191|unique:attributes',
      'label' => 'required|string|max:191',
      'type' => ['required', 'string', 'max:50', Rule::in(['text', 'select'])],
      'show_filter' => 'required|boolean',
      'style' => 'nullable|string|in:select,box,image',
      'public' => 'required|boolean',
      'status' => 'required|boolean',
      // 'values' => 'required_if:type,select|array',
      // 'values.*' => 'required_if:type,select|string|max:191'
    ];
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'show_filter' => $this->show_filter ? 1 : 0,
      'status' => $this->status ? 1 : 0,
      'public' => $this->status ? 1 : 0,
    ]);
  }
}

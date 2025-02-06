<?php

namespace Modules\Attribute\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Attribute\Entities\Attribute;
use Modules\Core\Helpers\Helpers;

class AttributeUpdateRequest extends FormRequest
{
  public function rules(): array
  {
    $isRequired = in_array($this->type, [Attribute::TYPE_SELECT]);

    return [
      'name' => ['required', 'string', 'max:191', Rule::unique('attributes')->ignore($this->route('attribute'))],
      'label' => 'required|string|max:191',
      'show_filter' => 'required|boolean',
      'public' => 'required|boolean',
      'status' => 'required|boolean',
      'style' => 'nullable|string|in:select,box,image',
      'values' => [Rule::requiredIf($isRequired), 'array'],
      'values.*' => [Rule::requiredIf($isRequired), 'string', 'max:191']
    ];
  }


  protected function prepareForValidation()
  {
    $this->merge([
      'show_filter' => $this->show_filter ? 1 : 0,
      'status' => $this->status ? 1 : 0,
      'public' => $this->public ? 1 : 0
    ]);
  }

  public function passedValidation()
  {
    if (empty($this->values) && in_array($this->type, [Attribute::TYPE_SELECT])) {
      throw Helpers::makeValidationException('حداقل داشتن یک مقدار الزامی است');
    }
    $attribute = $this->route('attribute');
    // مقادیری که حذف شده
    foreach ($this->input('deleted_values', []) as $editedValue) {
      $attributeValue = $attribute->values()->find($editedValue['id']);
      if (!$attributeValue) {
        return;
      }
      if (DB::table('attribute_variety')->where('attribute_value_id', $attributeValue->id)->exists()) {
        throw Helpers::makeValidationException('به علت وصل بودن مقدار ویژگی ' . $attributeValue->value .
          ' امکان حذف آن وجود ندازد');
      }
    }
  }
}

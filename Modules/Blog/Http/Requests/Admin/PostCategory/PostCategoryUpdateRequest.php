<?php

namespace Modules\Blog\Http\Requests\Admin\PostCategory;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostCategoryUpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => [
        'required',
        'string',
        'max:191',
        Rule::unique('post_categories')->ignore($this->route('postCategory'))
      ],
      'status' => 'required|boolean'
    ];
  }
  
  public function authorize(): bool
  {
    return true;
  }

  public function prepareForValidation()
  {
    $this->merge([
      'status' => $this->status ? 1 : 0
    ]);
  }
}

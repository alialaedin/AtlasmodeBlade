<?php

namespace Modules\Blog\Http\Requests\Admin\PostCategory;

use Illuminate\Foundation\Http\FormRequest;

class PostCategoryStoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:191|unique:post_categories',
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

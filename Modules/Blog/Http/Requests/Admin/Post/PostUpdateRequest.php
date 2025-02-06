<?php

namespace Modules\Blog\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Blog\Entities\Post;

class PostUpdateRequest extends FormRequest
{
  public function rules()
  {
    return [
      'title' => 'required|string|max:191',
      'post_category_id' => 'required|integer|exists:post_categories,id',
      'summary' => 'nullable|string|max:1000',
      'body' => 'required|string',
      'image' => 'nullable|image|max:8000',
      'meta_description' => 'nullable|string|max:1000',
      'status' => ['required', Rule::in(Post::getAvailableStatuses())],
      'special' => 'required|boolean',
      'published_at' => 'nullable|date_format:Y/m/d',
      'tags' => 'nullable|array',
      'tags.*' => 'string|max:191'
    ];
  }

  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'special' => $this->special ? 1 : 0
    ]);
  }
}

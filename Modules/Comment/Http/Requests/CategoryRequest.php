<?php

namespace Modules\Comment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'title' => 'required|string',
      'description' => 'required|string',
      'parent_id' => 'nullable|exists:categories,id',
      'model' => 'required|string',
    ];
  }
}

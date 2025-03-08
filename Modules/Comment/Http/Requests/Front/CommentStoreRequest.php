<?php

namespace Modules\Comment\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
  public function rules()
  {
    return [
      'name' => 'required|string|min:3',
      'email' => 'required|email',
      'body' => 'required|string',
      'parent_id' => 'nullable|exists:comments,id'
    ];
  }

  public function authorize()
  {
    return true;
  }

}

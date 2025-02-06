<?php

namespace Modules\Comment\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Comment\Entities\Comment;

class CommentAnswerRequest extends FormRequest
{
  public function rules()
  {
    return [
      'body' => 'required|string',
      'status' => 'required|in:' . implode(',', Comment::getAvailableStatuses())
    ];
  }

  public function prepareForValidation()
  {
    $this->merge(['status' => 'approved']);
  }
}
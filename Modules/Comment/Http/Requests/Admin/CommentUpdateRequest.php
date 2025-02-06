<?php

namespace Modules\Comment\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Comment\Entities\Comment;

class CommentUpdateRequest extends FormRequest
{
  private $nameRequired;
  private $emailRequired;

  public function rules()
  {
    return [
      'name' => [$this->nameRequired ? 'required' : 'nullable', 'string'],
      'email' => [$this->emailRequired ? 'required' : 'nullable', 'string'],
      'body' => 'required|string',
      'status' => 'required|in:' . implode(',', Comment::getAvailableStatuses())
    ];
  }

  public function authorize()
  {
    return true;
  }

  public function prepareForValidation()
  {
    $comment = Comment::query()->findOrFail($this->route('comment'));

    if ($comment->creator) {
      $this->request->remove('name');
      $this->request->remove('email');
      $this->nameRequired = false;
      $this->emailRequired = false;
    }
  }
}

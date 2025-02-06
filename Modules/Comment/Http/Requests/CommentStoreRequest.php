<?php

namespace Modules\Comment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;

class CommentStoreRequest extends FormRequest
{
  private $nameRequired = true;
  private $emailRequired = true;

  public function rules()
  {
    return [
      'name' => [$this->nameRequired ? 'required' : 'nullable', 'string'],
      'email' => [$this->emailRequired ? 'required' : 'nullable', 'email'],
      'body' => 'required|string',
      'parent_id' => 'nullable|exists:comments,id'
    ];
  }

  public function authorize()
  {
    return true;
  }

  public function prepareForValidation()
  {
    if (!$this->request->has('name') && $user = Helpers::getAuthenticatedUser()) {
      $this->nameRequired = false;
      $this->emailRequired = false;
      if ($user->name && $user->email) {
        $this->request->remove('name');
        $this->request->remove('email');
      }
    }
  }
}

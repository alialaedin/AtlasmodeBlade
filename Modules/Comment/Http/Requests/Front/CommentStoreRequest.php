<?php

namespace Modules\Comment\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Customer\Entities\Customer;

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

  protected function passedValidation()
  {
    if (Auth::guard('customer')->check()) {
      $this->merge([
        'creator_id'   => auth()->user()->id,
        'creator_type' => Customer::class
      ]);
    }
  }

  public function authorize()
  {
    return true;
  }

}

<?php

namespace Modules\Contact\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Rules\IranMobile;

class ContactRequest extends FormRequest
{
  public function rules()
  {
    return [
      'name' => 'required|string',
      'phone_number' => ['required', new IranMobile()],
      'subject' => 'required|string',
      'body' => 'required|string'
    ];
  }
}

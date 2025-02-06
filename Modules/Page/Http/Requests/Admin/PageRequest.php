<?php

namespace Modules\Page\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
  public function rules()
  {
    return [
      'title' => 'required|string',
      'text' => 'required|string'
    ];
  }
}

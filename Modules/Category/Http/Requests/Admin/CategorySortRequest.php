<?php

namespace Modules\Category\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategorySortRequest extends FormRequest
{
  public function rules()
  {
    return [
      'categories' => 'required',
    ];
  }
}

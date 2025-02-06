<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Rules\IranMobile;

class AdminUpdateRequest extends FormRequest
{
  public function rules()
  {
    $adminId = Helpers::getModelIdOnPut('admin');
    return [
      'name' => 'nullable|string|min:2',
      'username' => 'required|string|min:2|unique:admins,username,' . $adminId,
      'password' => 'nullable|string|min:6',
      'email' => 'nullable|email|min:2|unique:admins,email,' . $adminId,
      'mobile' => ['nullable', 'string', new IranMobile(), Rule::unique('admins', 'mobile')->ignore($adminId)],
      'role' => 'required|integer|exists:roles,id',
    ];
  }

  public function authorize()
  {
    return true;
  }
}

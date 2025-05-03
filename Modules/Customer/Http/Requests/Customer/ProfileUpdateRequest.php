<?php

namespace Modules\Customer\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Customer\Entities\Customer;

class ProfileUpdateRequest extends FormRequest
{
  public function rules()
  {
    return [
      'first_name' => 'nullable|string|max:191',
      'last_name' => 'nullable|string|max:191',
      'email' => 'nullable|email|max:191',
      'national_code' => 'nullable|digits:10',
      'gender' => ['nullable', Rule::in(Customer::getAvailableGenders())],
      'card_number' => 'nullable|digits:16',
      'birth_date' => 'nullable|date_format:Y-m-d|before:' . now(),
      'newsletter' => 'required|boolean',
      'foreign_national' => 'required|boolean',
      'password' => 'nullable|string|min:6|max:50'
    ];
  }

  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'newsletter' => $this->newsletter ? 1 : 0,
      'foreign_national' => $this->foreign_national ? 1 : 0,
    ]);
  }
}

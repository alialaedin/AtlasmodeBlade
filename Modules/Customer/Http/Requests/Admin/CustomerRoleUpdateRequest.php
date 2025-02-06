<?php

namespace Modules\Customer\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRoleUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'see_expired' => 'required|boolean'
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'see_expired' => $this->see_expired ? 1 : 0
        ]);
    }
}
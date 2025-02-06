<?php

namespace Modules\FAQ\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FAQUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'question' => 'required|string',
            'answer' => 'required|string',
            'status' => 'nullable|boolean|in:0,1'
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([
            'status' => (bool) $this->input('status', 0),
        ]);
    }
}

<?php

namespace Modules\ProductComment\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Admin\Entities\Admin;

class ProductCommentStoreRequest extends FormRequest
{
    public function rules()
    {
        $isRateRequired = auth('customer') ? true : false;

        return [
            'title' => 'nullable|string|min:5|max:195',
            'body' => 'required|string|min:10',
            'rate' => [
                Rule::requiredIf($isRateRequired),
                'integer',
                'digits_between:1,10'
            ],
            'show_customer_name' => 'required|boolean',
            'product_id' => 'required|integer|exists:products,id',
            'parent_id' => 'required|exists:product_comments,id',
        ];
    }

    public function prepareForValidation()
    {
        if (auth()->user() instanceof Admin) {
            $this->merge([
                'show_customer_name' => 0
            ]);
        }
    }
}

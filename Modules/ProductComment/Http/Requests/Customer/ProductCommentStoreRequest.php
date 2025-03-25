<?php

namespace Modules\ProductComment\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ProductCommentStoreRequest extends FormRequest
{
	public function prepareForValidation()
	{
		$this->merge([
			'show_customer_name' => $this->show_customer_name ? 1 : 0
		]);
	}

	public function rules()
	{
		return [
			'title' => 'nullable|string|min:5|max:195',
			'body' => 'required|string|min:10',
			'rate' => 'required|integer|digits_between:1,10',
			'show_customer_name' => 'required|boolean',
			'product_id' => 'required|integer|exists:products,id',
			'parent_id' => 'nullable|exists:product_comments,id'
		];
	}
}

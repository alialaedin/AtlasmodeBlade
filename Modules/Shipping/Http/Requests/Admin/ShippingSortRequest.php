<?php

namespace Modules\Shipping\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingSortRequest extends FormRequest
{
	public function rules()
	{
		return [
			'orders' => 'required|array',
			'orders.*' => 'required|exists:shippings,id'
		];
	}

	public function authorize()
	{
		return true;
	}
}

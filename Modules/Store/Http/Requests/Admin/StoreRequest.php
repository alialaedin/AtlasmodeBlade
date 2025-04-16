<?php

namespace Modules\Store\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Store\Entities\Store;

class StoreRequest extends FormRequest
{
	public function rules()
	{
		return [
			'variety_id' => 'bail|required|integer|exists:varieties,id',
			'description' => 'required|string|max:1000',
			'type' => ['required', Rule::in(Store::getAvailableTypes())],
			'quantity' => 'required|integer|min:1'
		];
	}
}

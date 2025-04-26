<?php

namespace Modules\Color\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ColorRangeStoreRequest extends FormRequest
{
	public function rules()
	{
		return [
			'title' => 'required|string|min:3|unique:color_ranges,title',
			'status' => 'required|boolean',
			'description' => 'required|string',
			'logo' => 'required|image|max:8000'
		];
	}

	protected function prepareForValidation()
	{
		$this->merge([
			'status' => $this->status ? 1 : 0
		]);
	}
}

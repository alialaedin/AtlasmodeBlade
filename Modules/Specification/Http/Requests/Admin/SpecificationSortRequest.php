<?php

namespace Modules\Specification\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SpecificationSortRequest extends FormRequest
{
	protected function prepareForValidation()
	{
		if ($this->has('ids')) {
			$this->merge([
				'ids' => json_decode($this->input('ids'), true),
			]);
		}
	}


	public function rules()
	{
		return [
			'ids' => 'required|array',
			'ids.*' => 'required|integer|exists:specifications,id'
		];
	}
}

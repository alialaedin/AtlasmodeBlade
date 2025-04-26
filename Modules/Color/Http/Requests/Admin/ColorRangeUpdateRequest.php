<?php

namespace Modules\Color\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ColorRangeUpdateRequest extends FormRequest
{
	public function rules()
	{
		$colorRangeId = $this->route('colorRange')->id;
		return [
			'title' => ['required', 'string', 'min:3', Rule::unique('color_ranges', 'title')->ignore($colorRangeId)],
			'status' => 'required|boolean',
			'description' => 'required|string',
			'logo' => 'nullable|image|max:8000'
		];
	}

	protected function prepareForValidation()
	{
		$this->merge([
			'status' => $this->status ? 1 : 0
		]);
	}
}

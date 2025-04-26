<?php

namespace Modules\Color\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ColorRangeSortRequest extends FormRequest
{
	public function rules()
	{
		return [
			'color_range_ids' => 'required|array',
			'color_range_ids.*' => 'required|exists:color_ranges,id'
		];
	}
}

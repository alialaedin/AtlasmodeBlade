<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RecommendationGroupUpdateRequest extends FormRequest 
{
  public function prepareForValidation()
	{
		$this->merge([
			'show_in_home' => $this->show_in_home ? 1 : 0,
			'show_in_filter' => $this->show_in_filter ? 1 : 0,
		]);
	}  

	public function rules()
	{
		return [
			'show_in_home' => 'required|boolean',
			'show_in_filter' => 'required|boolean',
		];
	}

	public function authorize()
	{
		return true;
	}
}

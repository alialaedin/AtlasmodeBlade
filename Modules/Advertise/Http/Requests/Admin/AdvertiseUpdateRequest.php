<?php

namespace Modules\Advertise\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Link\Services\LinkValidator;

class AdvertiseUpdateRequest extends FormRequest
{
	protected function prepareForValidation()
	{
		$this->merge([
			'status' => $this->status ? 1 : 0,
			'new_tab' => $this->new_tab ? 1 : 0
		]);
	} 

	public function rules()
	{
		return [
			'picture' => 'nullable|image',
			'link' => "nullable|string",
			'new_tab' => 'required|boolean',
			'status' => 'required|boolean',
			'start' => 'nullable',
			'end' => 'nullable',
			'linkable_id' => 'nullable',
			'linkable_type' => 'nullable',
		];
	}

	public function passedValidation()
	{
		(new LinkValidator($this))->validate();
	}

	public function authorize()
	{
		return true;
	}
}

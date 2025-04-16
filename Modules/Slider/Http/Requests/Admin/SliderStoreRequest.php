<?php


namespace Modules\Slider\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Link\Services\LinkValidator;
use Modules\Slider\Entities\Slider;

class SliderStoreRequest extends FormRequest
{
  public function rules()
  {
    $put = $this->isMethod('put') ? 'nullable' : 'required';
    return [
      'title' => 'nullable|string',
      'description' => 'nullable|string',
      'group' => ['required', 'string', Rule::in(Slider::getAvailableGroups())],
      'status' => 'required|boolean',
      'image' => "$put|image",
      'link' => 'nullable',
      'linkable_id' => 'nullable',
      'linkable_type' => 'nullable',
    ];
  }

  public function passedValidation()
  {
    (new LinkValidator($this))->validate();

    if ($this->isMethod('put') && $this->linkable_id) {
      $slider = $this->route('slider'); 
      $slider->link = null;
    }

  }

  public function prepareForValidation()
  {
    $this->merge([
      'status' => $this->input('status') ? true : false
    ]);
  }
}

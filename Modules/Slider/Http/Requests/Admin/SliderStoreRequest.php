<?php


namespace Modules\Slider\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;
use Modules\Link\Services\LinkValidator;

class SliderStoreRequest extends FormRequest
{
  public function rules()
  {
    $put = $this->isMethod('put') ? 'nullable' : 'required';

    return [
      'title' => 'nullable|string',
      'description' => 'nullable|string',
      'group' => 'required|string',
      'status' => 'required|boolean',
      'image' => "$put|image",
      'link' => 'nullable',
      'linkable_id' => 'nullable',
      'linkable_type' => 'nullable',
    ];
  }

  public function passedValidation()
  {
    $groupExists = in_array($this->group, config('slider.groups'));
    if (!$groupExists) {
      throw Helpers::makeValidationException('گروه مورد نظر یافت نشد');
    }
    (new LinkValidator($this))->validate();
  }

  public function prepareForValidation()
  {
    $this->merge([
      'status' => $this->input('status') ? true : false
    ]);
  }
}

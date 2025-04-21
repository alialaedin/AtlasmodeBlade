<?php

namespace Modules\Menu\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Link\Services\LinkValidator;

class MenuUpdateRequest extends FormRequest
{
  public function rules()
  {
    return [
      'title' => 'required',
      'link' => "nullable|string",
      'new_tab' => 'nullable',
      'parent_id' => 'nullable|exists:menu_items,id',
      'status' => 'nullable|boolean',
      'group_id' => 'required|exists:menu_groups,id',
      'icon' => 'nullable|image'
    ];
  }

  public function passedValidation()
  {
    (new LinkValidator($this))->validate();
  }

  public function prepareForValidation()
  {
    $this->merge([
      'status' => $this->status ? 1 : 0,
      'new_tab' => $this->new_tab ? 1 : 0,
      'parent_id' => $this->parent_id == 'none-parent' ? null : $this->parent_id
    ]);
  }
}

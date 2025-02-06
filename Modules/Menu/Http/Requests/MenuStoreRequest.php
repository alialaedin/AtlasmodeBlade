<?php

namespace Modules\Menu\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Advertise\Entities\Advertise;
use Modules\Blog\Entities\Post;
use Modules\Category\Entities\Category;
use Modules\Flash\Entities\Flash;
use Modules\Page\Entities\Page;
use Modules\Product\Entities\Product;

class MenuStoreRequest extends FormRequest
{
  public $toggle = false;

  public function rules()
  {
    return [
      'title' => 'required',
      'link' => "nullable|string",
      'new_tab' => 'nullable',
      'parent_id' => 'nullable|exists:menu_items,id',
      'status' => 'nullable',
      'group_id' => 'required|exists:menu_groups,id',
      'icon' => 'nullable|image'
    ];
  }
  public function prepareForValidation()
  {
    if (filled($this->linkable_type) && $this->linkable_type != 'self_link') {
      $array = [
        'IndexModules\Blog\Entities\Post' => Post::class,
        'Modules\Blog\Entities\Post' => Post::class,
        'Modules\Category\Entities\Category' => Category::class,
        'Modules\Product\Entities\Product' => Product::class,
        'IndexModules\Product\Entities\Product' => Product::class,
        'Modules\Flash\Entities\Flash' => Flash::class,
        'Modules\Page\Entities\Page' => Page::class,
        'IndexModules\Advertise\Entities\Advertise' => Advertise::class,
        'Modules\Advertise\Entities\Advertise' => Advertise::class,
        'IndexCustom\AboutUs' => 'Custom\AboutUs',
        'IndexCustom\ContactUs' => 'Custom\ContactUs',
      ];
      $this->merge([
        'linkable_type' => $array[$this->linkable_type]
      ]);
    }
    $this->merge([
      'status' => (bool) $this->input('status', 0),
      'new_tab' => (bool) $this->input('new_tab', 0)
    ]);
  }
}

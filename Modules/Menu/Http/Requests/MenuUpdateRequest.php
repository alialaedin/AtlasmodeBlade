<?php

namespace Modules\Menu\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Advertise\Entities\Advertise;
use Modules\Blog\Entities\Post;
use Modules\Category\Entities\Category;
use Modules\Core\Helpers\Helpers;
use Modules\Flash\Entities\Flash;
use Modules\Menu\Entities\MenuItem;
use Modules\Page\Entities\Page;
use Modules\Product\Entities\Product;

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
    if ($this->input('parent_id')) {
      $parentMenu = MenuItem::findOrFail($this->input('parent_id'));
      if ($parentMenu->group_id != $this->input('group_id')) {
        throw Helpers::makeValidationException('پدر از گروه دیگر است !');
      }
      $menuItemId = Helpers::getModelIdOnPut('menu_item');
      if (!$menuItemId) {
        return;
      }
      if ($parentMenu->id == $menuItemId) {
        throw Helpers::makeValidationException('خودش نمیتونه پدر خودش باشه !');
      }
      if ($parentMenu->parent_id == $menuItemId) {
        throw Helpers::makeValidationException('نمیشه هردو پدر هم باشن !');
      }
      // جلوگیری از لوپ بینهایت
      $parentId = $parentMenu->parent_id;
      while ($parentId != null) {
        $tempMenu = MenuItem::find($parentId);
        if (!$tempMenu) {
          continue;
        }
        if ($tempMenu->parent_id == $menuItemId) {
          throw Helpers::makeValidationException('انتخاب پدر از نوه نتیجه نبیره و ... مجاز نیست');
        }
        $parentId = $tempMenu->parent_id;
      }
    }
  }
  public function prepareForValidation()
  {
    if (filled($this->linkable_type) && $this->linkable_type != 'self_link' && $this->linkable_type != 'self_link2') {
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

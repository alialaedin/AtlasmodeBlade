<?php

namespace Modules\Menu\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Link\Http\Controllers\Admin\LinkController;
use Modules\Menu\Entities\MenuGroup;
use Modules\Menu\Entities\MenuItem;
use Modules\Menu\Http\Requests\MenuSortRequest;
use Modules\Menu\Http\Requests\MenuStoreRequest;
use Modules\Menu\Http\Requests\MenuUpdateRequest;

class MenuItemController extends Controller
{
  private function getAllLinkables()
  {
    $linkableData = (new LinkController)->create();
    $dataArray = $linkableData->getData(true);

    $linkables = [];
    if ($dataArray['success'] && isset($dataArray['data']['linkables'])) {
      $linkables = $dataArray['data']['linkables'];
    }
    return $linkables;
  }

  public function index(MenuGroup $menuGroup)
  {
    $menuItems = MenuItem::getMenusForAdmin($menuGroup);
    return view('menu::admin.index', compact(['menuItems', 'menuGroup']));
  }

  public function create(MenuGroup $menuGroup)
  {
    $linkables = $this->getAllLinkables();
    $menuItems = MenuItem::getMenusForUpdateCreate($menuGroup);
    return view('menu::admin.create', compact(['menuGroup', 'linkables', 'menuItems']));
  }

  public function store(MenuStoreRequest $request)
  {
    $menuItem = MenuItem::storeOrUpdate($request);
    return redirect()->route('admin.menus.index', $menuItem->group)->with('success', 'منو با موفقیت اضافه شد.');
  }

  public function edit(MenuItem $menuItem)
  {
    $linkables = $this->getAllLinkables();
    $menuItems = MenuItem::getMenusForUpdateCreate($menuItem->group);
    $menuGroup = $menuItem->group;

    return view('menu::admin.edit', compact(['linkables', 'menuItems', 'menuItem', 'menuGroup']));
  }

  public function update(MenuUpdateRequest $request, MenuItem $menuItem)
  {
    MenuItem::storeOrUpdate($request, $menuItem);
    return redirect()->route('admin.menus.index', $menuItem->group)->with('success', 'منو با موفقیت به روزرسانی شد.');
  }

  public function sort(MenuSortRequest $request)
  {
    MenuItem::sort($request->menu_items);
    return response()->success('آیتم های منو با موفقیت مرتب سازی شد');
  }

  public function destroy(MenuItem $menuItem)
  {
    $menuItem->delete();
    ActivityLogHelper::deletedModel('منو حذف شد', $menuItem);
    return redirect()->back()->with('success', 'منو با موفقیت حذف شد.');
  }
}

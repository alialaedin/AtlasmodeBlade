<?php

namespace Modules\Menu\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Link\Http\Controllers\Admin\LinkController;
use Modules\Menu\Entities\MenuGroup;
use Modules\Menu\Entities\MenuItem;
use Modules\Menu\Http\Requests\MenuStoreRequest;
use Modules\Menu\Http\Requests\MenuUpdateRequest;

class MenuItemController extends Controller
{
  public function groups()
  {
    $menuGroups = MenuGroup::all('id', 'title');
    $group_labels = $menuGroups->map(function ($menuGroup) {
      if (Lang::has('core::groups.' . $menuGroup->title)) {
        return trans('core::groups.' . $menuGroup->title);
      }
      return false;
    });

    if (request()->header('Accept') == 'application/json') {
      return response()->success('', [
        'groups' => MenuGroup::all('id', 'title'),
        'group_labels' => $group_labels
      ]);
    }
    return view('menu::admin.groups', ['groups' => MenuGroup::all('id', 'title'), 'group_labels' => $group_labels]);
  }

  public function index($groupId, $id = null)
  {
    $linkableData = (new LinkController)->create();

    $dataArray = $linkableData->getData(true);

    if ($dataArray['success'] && isset($dataArray['data']['linkables'])) {
      $linkables = $dataArray['data']['linkables'];
    }
    if ($id) {
      $parentMenu = MenuItem::find($id);
    } else {
      $parentMenu = null;
    }
    $menu_items = MenuItem::where('parent_id', $id)->where('group_id', $groupId)->orderBy('order', 'asc')->with('children')->get();

    if (request()->header('Accept') == 'application/json') {
      return response()->success('', ['menu_items' => $menu_items]);
    }
    return view('menu::admin.index', compact('menu_items', 'linkables', 'groupId', 'parentMenu'));
  }
  public function store(MenuStoreRequest $request)
  {
    $menu = new MenuItem();
    $menu->fill($request->all());
    $menu->save();
    ActivityLogHelper::storeModel('منو ثبت شد', $menu);
    if ($request->hasFile('icon')) {
      $menu->addIcon($request->file('icon'));
    }
    $menu->refresh();

    if (request()->header('Accept') == 'application/json') {
      return response()->success('منو با موفقیت اضافه شد', compact('menu'));
    }

    return redirect()->back()->with('success', 'منو با موفقیت اضافه شد.');
  }
  public function update(MenuUpdateRequest $request, MenuItem $menuItem)
  {
    if ($request->linkable_id) {
      $menuItem->link = null;
    }
    $request->all();
    $menuItem->update($request->all());
    if ($request->hasFile('icon')) {
      $menuItem->addIcon($request->file('icon'));
    }
    ActivityLogHelper::updatedModel('منو بروز شد', $menuItem);

    if (request()->header('Accept') == 'application/json') {
      return response()->success('منو با موفقیت بروز شد', ['menu_item' => $menuItem]);
    }
    return redirect()->back()->with('success', 'منو با موفقیت به روزرسانی شد.');
  }
  public function sort(Request $request)
  {
    $orderIds = $request->orders;
    $items = MenuItem::whereIn('id', $orderIds)->get();

    $itemMap = $items->keyBy('id');

    foreach ($orderIds as $index => $id) {
      if (isset($itemMap[$id])) {
        $item = $itemMap[$id];
        $item->order = $index + 1;
        $item->save();
      }
    }

    return redirect()->back()->with('success', 'آیتم های منو با موفقیت مرتب سازی شد.');
  }

  public function destroy($id)
  {
    $menuItem = MenuItem::findOrFail($id);
    $menuItem->delete();
    ActivityLogHelper::deletedModel('منو حذف شد', $menuItem);

    return redirect()->back()->with('success', 'منو با موفقیت حذف شد.');
  }
}

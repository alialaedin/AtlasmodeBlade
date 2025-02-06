<?php

namespace Modules\Color\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Color\Entities\Color;
use Modules\Color\Http\Requests\Admin\ColorStoreRequest;
use Modules\Color\Http\Requests\Admin\ColorUpdateRequest;

class ColorController extends Controller
{
  public function index()
  {
    $colors = Color::query()
      ->select(['id', 'name', 'code', 'status', 'created_at'])
      ->latest('id')
      ->filters()
      ->paginate()
      ->withQueryString();

    return view('color::admin.index', compact('colors'));
  }

  public function store(ColorStoreRequest $request)
  {
    $color = Color::create($request->all());
    ActivityLogHelper::storeModel('رنگ ثبت شد', $color);

    return redirect()->back()->with('success', 'رنگ با موفقیت ثبت شد.');
  }

  public function update(ColorUpdateRequest $request, Color $color)
  {
    $color->update($request->all());
    ActivityLogHelper::updatedModel('رنگ بروز شد', $color);

    return redirect()->back()->with('success', 'رنگ با موفقیت به روزرسانی شد.');
  }

  public function destroy(Color $color)
  {
    $color->delete();
    ActivityLogHelper::deletedModel('رنگ حذف شد', $color);

    return redirect()->back()->with('success', 'رنگ با موفقیت حذف شد.');
  }
}

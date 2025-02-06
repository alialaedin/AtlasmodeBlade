<?php

namespace Modules\Brand\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Brand\Entities\Brand;
use Modules\Brand\Http\Requests\Admin\BrandStoreRequest;
use Modules\Brand\Http\Requests\Admin\BrandUpdateRequest;

class BrandController extends Controller
{
  public function index()
  {
    $brands = Brand::query()
      ->select(['id', 'name', 'status', 'creator_id', 'updater_id', 'show_index', 'description', 'created_at'])
      ->with([
        'creator' => fn ($q) => $q->select(['id', 'name']),
        'updater' => fn ($q) => $q->select(['id', 'name'])
      ])
      ->latest('id')
      ->filters()
      ->get();

    return view('brand::admin.index', compact('brands'));
  }

  public function store(BrandStoreRequest $request)
  {
    $brand = Brand::query()->create($request->all());
    if ($request->hasFile('image'))
      $brand->addImage($request->image);
    ActivityLogHelper::simple('برند ثبت شد', 'store', $brand);

    return redirect()->back()->with('success', 'برند شما با موفقیت ایجاد شد.');
  }

  public function update(BrandUpdateRequest $request, Brand $brand)
  {
    $brand->update($request->all());
    if ($request->hasFile('image'))
      $brand->addImage($request->image);
    ActivityLogHelper::updatedModel('برند بروزرسانی شد', $brand);

    return redirect()->back()->with('success', 'برند مورد نظر بروزرسانی شد.');
  }

  public function destroy(Brand $brand)
  {
    $brand->delete();
    ActivityLogHelper::deletedModel('برند حذف شد', $brand);

    return redirect()->back()->with('success', 'برند با موفقیت حذف شد.');
  }
}

<?php

namespace Modules\Area\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Modules\Area\Entities\Province;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Area\Http\Requests\Admin\ProvinceStoreRequest;
use Modules\Area\Http\Requests\Admin\ProvinceUpdateRequest;

class ProvinceController extends Controller
{
  public function index(): View|JsonResponse
  {
    $provinces = Province::query()
      ->select(['id', 'name', 'status', 'created_at'])
      ->filters()
      ->withCount('cities')
      ->latest('id')
      ->paginate()
      ->withQueryString();

    return view('area::admin.province.index', compact('provinces'));
  }

  public function show(Province $province): View|JsonResponse
  {
    $province->load('cities');

    return view('area::admin.province.show', compact('province'));
  }

  public function store(ProvinceStoreRequest $request): JsonResponse|RedirectResponse
  {
    $province = Province::query()->create($request->validated());
    ActivityLogHelper::simple('استان ثبت شد', 'store', $province);

    return redirect()->back()->with('success', 'استان با موفقیت ثبت شد.');
  }

  public function update(ProvinceUpdateRequest $request, Province $province): JsonResponse|RedirectResponse
  {
    $province->update($request->all());
    ActivityLogHelper::updatedModel('استان بروز شد', $province);

    return redirect()->back()->with('success', 'استان با موفقیت ویرایش شد.');
  }

  public function destroy(Province $province): JsonResponse|RedirectResponse
  {
    $province->delete();
    ActivityLogHelper::deletedModel('استان حذف شد', $province);

    return redirect()->back()->with('success', 'استان با موفقیت حذف شد.');
  }
}

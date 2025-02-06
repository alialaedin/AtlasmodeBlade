<?php

namespace Modules\Area\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Area\Entities\City;
use Modules\Area\Entities\Province;
use Modules\Area\Http\Requests\Admin\CityStoreRequest;
use Modules\Area\Http\Requests\Admin\CityUpdateRequest;

class CityController extends Controller
{
  public function index(): View|JsonResponse
  {
    $cities = City::query()
      ->select(['id', 'name', 'province_id', 'created_at', 'status'])
      ->latest('id')
      ->filters()
      ->paginate();

    $provinces = Province::getAllProvinces();

    return view('area::admin.city.index', compact(['cities', 'provinces']));
  }

  public function store(CityStoreRequest $request): RedirectResponse
  {
    $city = City::query()->create($request->validated());
    ActivityLogHelper::simple('شهر ثبت شد', 'store', $city);

    return redirect()->back()->with('success', 'شهر با موفقیت ثبت شد.');
  }

  public function update(CityUpdateRequest $request, City $city): RedirectResponse
  {
    $city->update($request->validated());
    ActivityLogHelper::updatedModel('شهر بروزرسانی شد', $city);

    return redirect()->back()->with('success', 'شهر با موفقیت بروز شد.');
  }

  public function destroy(City $city): RedirectResponse
  {
    $city->delete();
    ActivityLogHelper::deletedModel('شهر حذف شد', $city);

    return redirect()->back()->with('success', 'شهر با موفیت حذف شد.');
  }
}
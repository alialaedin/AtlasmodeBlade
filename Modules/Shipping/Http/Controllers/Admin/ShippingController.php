<?php

namespace Modules\Shipping\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Area\Entities\City;
use Modules\Area\Entities\Province;
use Modules\Shipping\Entities\Shipping;
use Modules\Shipping\Http\Requests\Admin\ShippingCityAssignRequest;
use Modules\Shipping\Http\Requests\Admin\ShippingStoreRequest;
use Modules\Shipping\Http\Requests\Admin\ShippingUpdateRequest;

class ShippingController extends Controller
{
  public function index()
  {
    $shippings = Shipping::query()->orderBy('order', 'DESC')->filters()->get();
    $totalShipping = count($shippings);

    return view('shipping::admin.shipping.index', compact('shippings', 'totalShipping'));
  }

  public function create()
  {
    $provinces = Province::query()->select(['id', 'name'])->get();

    return view('shipping::admin.shipping.create', compact('provinces'));
  }


  public function store(ShippingStoreRequest $request)
  {
    DB::beginTransaction();
    try {
      $shipping = Shipping::query()->create($request->all());

      if ($request->hasFile('logo')) {
        $shipping->addImage($request->logo);
      }
      $shipping->load('media');

      if ($request->provinces) {
        $shipping->setProvinces($request);
      }

      if ($request->customer_roles) {
        $shipping->setCustomerRoles($request);
      }

      DB::commit();
    } catch (Exception $exception) {
      DB::rollBack();
      Log::error($exception->getTraceAsString());
      return redirect()->back()->with('error', 'مشکلی در ثبت حمل و نقل به وجود آمده است');
    }

    ActivityLogHelper::storeModel(' سرویس حمل و نقل ثبت شد', $shipping);

    return redirect()->route('admin.shippings.index')->with('success', 'سرویس حمل و نقل با موفقیت ثبت شد');
  }


  public function show($id)
  {
    $shipping = Shipping::query()->findOrFail($id);

    return view('shipping::admin.shipping.show', compact('shipping'));
  }


  public function sort(Request $request)
  {
    $request->validate([
      'orders' => 'required|array',
      'orders.*' => 'required|exists:shippings,id'
    ]);
    $order = 99;
    foreach ($request->input('orders') as $itemId) {
      $model = Shipping::query()->find($itemId);
      if (!$model) {
        continue;
      }
      $model->order = $order--;
      $model->save();
    }

    return redirect()->back()->with('success', 'مرتب سازی با موفقیت انجام شد');
  }

  public function edit(Shipping $shipping)
  {
    $provinces = Province::query()->select('id', 'name')->get();

    return view('shipping::admin.shipping.edit', compact(['shipping', 'provinces']));
  }

  public function update(ShippingUpdateRequest $request, $id)
  {
    DB::beginTransaction();
    try {

      $shipping = Shipping::query()->findOrFail($id);

      $shipping->fill($request->all());
      if ($request->hasFile('logo')) {
        $shipping->addImage($request->logo);
      }
      $shipping->save();

      $shipping->setProvinces($request);
      $shipping->setCustomerRoles($request);

      DB::commit();
    } catch (Exception $exception) {
      DB::rollBack();
      Log::error($exception->getTraceAsString());
      return redirect()->back()->with('error', 'مشکلی در به روزرسانی حمل و نقل به وجود آمده است');
    }

    ActivityLogHelper::updatedModel(' سرویس حمل و نقل ویرایش شد', $shipping);

    return redirect()->route('admin.shippings.index')->with('success', 'سرویس حمل و نقل با موفقیت به روزرسانی شد');
  }

  public function assignCities(ShippingCityAssignRequest $request, $id)
  {

    $shipping = Shipping::query()->findOrFail($id);
    $cities = [];

    foreach ($request->input('cities') ?? [] as $city) {
      $cityModel = City::find($city['id']);
      if (!$cityModel || !$shipping->provinces()->where('provinces.id', $cityModel->province_id)->exists()) {
        continue;
      }
      $cities[$city['id']] = [
        'price' => $city['price']
      ];
    }
    $shipping->cities()->sync($cities);

    return redirect()->back()->with('success', 'عملیات با موفقیت انجام شد');
  }

  public function destroy($id)
  {
    $shipping = Shipping::query()->findOrFail($id);
    $shipping->delete();
    ActivityLogHelper::deletedModel(' سرویس حمل و نقل حذف شد', $shipping);

    return redirect()->back()->with('success', 'سرویس حمل و نقل با موفقیت حذف شد');
  }

  public function cities(Shipping $shipping)
  {
    return view('shipping::admin.shipping.cities', compact('shipping'));
  }
}

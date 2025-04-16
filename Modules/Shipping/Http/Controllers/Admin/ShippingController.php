<?php

namespace Modules\Shipping\Http\Controllers\Admin;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Area\Entities\Province;
use Modules\Shipping\Entities\Shipping;
use Modules\Shipping\Http\Requests\Admin\ShippingCityAssignRequest;
use Modules\Shipping\Http\Requests\Admin\ShippingSortRequest;
use Modules\Shipping\Http\Requests\Admin\ShippingStoreRequest;
use Modules\Shipping\Http\Requests\Admin\ShippingUpdateRequest;

class ShippingController extends Controller
{
  public function index()
  {
    $shippings = Shipping::query()->orderBy('order', 'DESC')->filters()->get();
    return view('shipping::admin.shipping.index', compact('shippings'));
  }

  public function create()
  {
    $provinces = Province::getAllProvinces();
    return view('shipping::admin.shipping.create', compact('provinces'));
  }

  public function store(ShippingStoreRequest $request)
  {
    DB::beginTransaction();
    try {
      Shipping::createOrUpdate($request);
      DB::commit();
    } catch (Exception $exception) {
      DB::rollBack();
      Log::error($exception->getTraceAsString());
      return redirect()->back()->with('error', 'مشکلی در ثبت حمل و نقل به وجود آمده است');
    }
    return redirect()->route('admin.shippings.index')->with('success', 'سرویس حمل و نقل با موفقیت ثبت شد');
  }


  public function show(Shipping $shipping)
  {
    return view('shipping::admin.shipping.show', compact('shipping'));
  }


  public function sort(ShippingSortRequest $request)
  {
    Shipping::sort($request);
    return redirect()->back()->with('success', 'مرتب سازی با موفقیت انجام شد');
  }

  public function edit(Shipping $shipping)
  {
    $provinces = Province::getAllProvinces();
    return view('shipping::admin.shipping.edit', compact(['shipping', 'provinces']));
  }

  public function update(ShippingUpdateRequest $request, Shipping $shipping)
  {
    DB::beginTransaction();
    try {
      Shipping::createOrUpdate($request, $shipping);
      DB::commit();
    } catch (Exception $exception) {
      DB::rollBack();
      Log::error($exception->getTraceAsString());
      return redirect()->back()->with('error', 'مشکلی در به روزرسانی حمل و نقل به وجود آمده است');
    }
    return redirect()->route('admin.shippings.index')->with('success', 'سرویس حمل و نقل با موفقیت به روزرسانی شد');
  }

  public function assignCities(ShippingCityAssignRequest $request, Shipping $shipping)
  {
    Shipping::assingCities($shipping, $request);
    return redirect()->back()->with('success', 'عملیات با موفقیت انجام شد');
  }

  public function destroy(Shipping $shipping)
  {
    $shipping->delete();
    ActivityLogHelper::deletedModel(' سرویس حمل و نقل حذف شد', $shipping);
    return redirect()->back()->with('success', 'سرویس حمل و نقل با موفقیت حذف شد');
  }

  public function cities(Shipping $shipping)
  {
    return view('shipping::admin.shipping.cities', compact('shipping'));
  }
}

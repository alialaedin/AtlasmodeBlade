<?php

namespace Modules\Customer\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Area\Entities\City;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Http\Requests\Admin\AddressStoreRequest;
use Modules\Customer\Http\Requests\Admin\AddressUpdateRequest;

class AddressController extends Controller
{
  public function store(AddressStoreRequest $request)
  {
    $customer = Customer::query()->find($request->customer_id);
    $address = $customer->addresses()->create($request->all());
    $address->load(['city.province', 'customer']);

    return redirect()->back()->with('success', 'آدرس با موفقیت ثبت شد');
  }

  public function update(AddressUpdateRequest $request, $id)
  {
    $customer = Customer::query()->findOrFail($request->customer_id);
    $address = $customer->addresses()->findOrFail($id);

    $address->update($request->all());
    $address->load(['customer']);
    return redirect()->back()->with('success', 'آدرس با موفقیت بروزرسانی شد');
  }

  public function destroy($customerId, $addressId)
  {
    $customer = Customer::query()->findOrFail($customerId);
    $address = $customer->addresses()->findOrFail($addressId);

    $address->delete();
    return redirect()->back()->with('success', 'آدرس با موفقیت حذف شد');
  }

  public function getCities(Request $request)
  {
    $cities = City::where('province_id', $request->provinceId)->get();
    return response()->json($cities);
  }
}

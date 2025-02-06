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
  /**
   * Store a newly created resource in storage.
   *
   * @param AddressStoreRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(AddressStoreRequest $request)
  {
    $customer = Customer::query()->find($request->customer_id);
    $address = $customer->addresses()->create($request->all());
    $address->load(['city.province', 'customer']);

    return response()->success('آدرس با موفقیت ثبت شد.', compact('address'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param AddressUpdateRequest $request
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(AddressUpdateRequest $request, $id)
  {
    $customer = Customer::query()->findOrFail($request->customer_id);
    $address = $customer->addresses()->findOrFail($id);

    $address->update($request->all());
    $address->load(['customer']);

    return response()->success('آدرس با موفقیت به روزرسانی شد.', compact('address'));
  }

  /**
   * Remove the specified resource from storage.
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy($customerId, $addressId)
  {
    $customer = Customer::query()->findOrFail($customerId);
    $address = $customer->addresses()->findOrFail($addressId);

    $address->delete();

    return response()->success('آدرس با موفقیت حذف شد.');
  }

  public function getCities(Request $request)
  {
    $cities = City::where('province_id', $request->provinceId)->get();

    return response()->json($cities);
  }
}

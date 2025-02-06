<?php

namespace Modules\Customer\Http\Controllers\Customer;

use Illuminate\Routing\Controller;
use Modules\Customer\Entities\Address;
use Modules\Customer\Http\Requests\Customer\AddressStoreRequest;
use Modules\Customer\Http\Requests\Customer\AddressUpdateRequest;

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
    $customer = $request->user();
    $address = $customer->addresses()->create($request->all());
    $address->load('city.province');

    return response()->success('آدرس با موفقیت ثبت شد.', compact('address'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param AddressUpdateRequest $request
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(AddressUpdateRequest $request, Address $address)
  {
    if ($address->customer_id != auth()->user()->id) {
      return response()->error('Forbidden', [], 403);
    }

    $address->update($request->all());
    $address->load('city.province');

    return response()->success('آدرس با موفقیت به روزرسانی شد.', compact('address'));
  }

  /**
   * Remove the specified resource from storage.
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy($id)
  {
    $address = Address::findOrFail($id);
    if ($address->customer_id != auth()->user()->id) {
      return response()->error('Forbidden', [], 403);
    }

    $address->delete();

    return response()->success('آدرس با موفقیت حذف شد.');
  }
}

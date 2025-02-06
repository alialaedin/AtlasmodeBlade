<?php

namespace Modules\Customer\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Customer\Entities\CustomerRole;
use Modules\Customer\Http\Requests\Admin\CustomerRoleStoreRequest;
use Modules\Customer\Http\Requests\Admin\CustomerRoleUpdateRequest;

class CustomerRoleController extends Controller
{
  public function index()
  {
    $customerRoles = CustomerRole::query()->latest('id')->get();

    return view('customer::admin.customer-role.index', compact('customerRoles'));
  }

  public function store(CustomerRoleStoreRequest $request)
  {
    $customerRoles = CustomerRole::query()->create($request->all());
    ActivityLogHelper::storeModel('نقش مشتری با موفقیت ایجاد شد', $customerRoles);

    return redirect()->back()->with('success', 'نقش مشتری با موفقیت ایجاد شد');
  }

  public function update(CustomerRoleUpdateRequest $request, CustomerRole $customerRole)
  {
    $customerRole->update($request->all());
    ActivityLogHelper::updatedModel('نقش مشتری با موفقیت بروزرسانی شد', $customerRole);

    return redirect()->back()->with('success', 'نقش مشتری با موفقیت بروزرسانی شد');
  }

  public function destroy(CustomerRole $customerRole)
  {
    $customerRole->delete();
    ActivityLogHelper::deletedModel('نقش مشتری با موفقیت حذف شد', $customerRole);
    
    return redirect()->back()->with('success', 'نقش مشتری با موفقیت حذف شد');
  }
}

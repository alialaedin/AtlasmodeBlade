<?php

namespace Modules\Admin\Http\Controllers\Admin;

use Illuminate\Support\Facades\Hash;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Admin\Entities\Admin;
use Modules\Admin\Http\Requests\AdminStoreRequest;
use Modules\Admin\Http\Requests\AdminUpdateRequest;
use Modules\Core\Entities\Role;
use Modules\Core\Helpers\Helpers;
use Spatie\Activitylog\Models\Activity;

class AdminController
{
  public function index()
  {
    $admins = Admin::query()
      ->select(['id', 'name', 'username', 'mobile', 'email', 'created_at'])
      ->with('roles.permissions')
      ->latest('id')
      ->get();

    return view('admin::admin.index', compact('admins'));
  }

  public function show(Admin $admin) 
  {
    $activities = Activity::query()
			->select('id', 'causer_id', 'description', 'created_at')
			->where('causer_id', $admin->id)
			->latest('id')
			->paginate(15);

		$totalActivity = $activities->total();

		return view('admin::admin.show', compact('admin', 'activities', 'totalActivity'));
  }

  public function create()
  {
    $roles = Role::select('id', 'name', 'label')->get();

    return view('admin::admin.create', compact('roles'));
  }

  public function store(AdminStoreRequest $request)
  {
    $admin = Admin::query()->create($request->all());
    $role = Role::findOrFail($request->role);

    $admin->assignRole($role);

    ActivityLogHelper::simple('ادمین ساخته شد', 'store', $admin);

    return redirect()->route('admin.admins.index')->with('success', 'ادمین با موفقیت ثبت شد.');
  }

  public function edit(Admin $admin)
  {
    $adminRolesName = $admin->getRoleNames()->first();

    if ($adminRolesName == 'super_admin') {
      $roles = Role::select('id', 'name', 'label')->where('name', 'super_admin')->get();
    } else {
      $roles = Role::select('id', 'name', 'label')->where('name', '!=', 'super_admin')->get();
    }
    return view('admin::admin.edit', compact('roles', 'adminRolesName', 'admin'));
  }

  public function update(AdminUpdateRequest $request, Admin $admin)
  {
    $password = filled($request->password) ? $request->password : $admin->password;

    $admin->update([
      'name' => $request->name,
      'username' => $request->username,
      'mobile' => $request->mobile,
      'password' => Hash::make($password),
    ]);

    $role = Role::findOrFail($request->role);
    $admin->assignRole($role);
    ActivityLogHelper::updatedModel('ادمین ویرایش شد', $admin);

    return redirect()->route('admin.admins.index')->with('success', 'ادمین با موفقیت به روزرسانی شد.');
  }

  public function destroy($id)
  {
    $admin = Admin::query()->findOrFail($id);

    if ($admin->hasRole('super_admin')) {
      throw Helpers::makeValidationException('شما مجاز به حذف سوپر ادمین نمیباشید', 'role');
    }

    $admin->delete();
    ActivityLogHelper::updatedModel('ادمین حذف شد', $admin);

    return redirect()->route('admin.admins.index')->with('success', 'ادمین با موفقیت حذف شد.');
  }
}

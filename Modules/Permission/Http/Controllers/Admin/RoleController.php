<?php

namespace Modules\Permission\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Core\Entities\Role;
use Modules\Core\Entities\Permission;
use Illuminate\Database\Eloquent\Collection;
use Modules\Permission\Http\Requests\Admin\RoleStoreRequest;
use Modules\Permission\Http\Requests\Admin\RoleUpdateRequest;
use Illuminate\Contracts\Support\Renderable;

class RoleController extends Controller
{
  private function permissions(): Collection
  {
    return Permission::query()->oldest('id')->select(['id', 'name', 'label'])->get();
  }
  public function index()
  {
    $roles = Role::query()
      ->latest('id')
      ->select(['id', 'name', 'label', 'created_at'])
      ->with('permissions')
      ->paginate(15);

    return view('permission::admin.role.index', compact('roles'));
  }
  public function create(): Renderable
  {
    $permissions = $this->permissions();

    return view('permission::admin.role.create', compact('permissions'));
  }

  public function store(RoleStoreRequest $request)
  {
    $role = Role::query()->create($request->only('name', 'label', 'guard_name'));
    $role->givePermissionTo($request->permissions);
    ActivityLogHelper::storeModel('نقش ثبت شد', $role);

    return redirect()->route('admin.roles.index')->with('success', 'نقش با موفقیت ثبت شد.');
  }

  public function edit(Role $role)
  {
    $permissions = $this->permissions();

    return view('permission::admin.role.edit', compact('permissions', 'role'));
  }

  public function update(RoleUpdateRequest $request, $id)
  {
    $role = Role::query()->findOrFail($id);
    $role->update($request->only('name', 'label', 'guard_name'));
    $role->syncPermissions($request->permissions);
    ActivityLogHelper::updatedModel('نقش بروز شد', $role);

    return redirect()->route('admin.roles.index')->with('success', 'نقش با موفقیت بروزرسانی شد.');
  }

  public function destroy(Role $role)
  {
    if (!empty($role->admins['items'])) {
      return redirect()->route('admin.roles.index')->with('danger', 'نقش به ادمینی وصل هست');
    }
    $permissions = $role->permissions;
    if ($role->delete()) {
      foreach ($permissions as $permission) {
        $role->revokePermissionTo($permission);
      }
    }
    ActivityLogHelper::deletedModel('نقش حذف شد', $role);

    return redirect()->route('admin.roles.index')->with('success', 'نقش با موفقیت حذف شد.');
  }
}

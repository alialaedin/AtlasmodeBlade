<?php

namespace Modules\Unit\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Unit\Entities\Unit;
use Modules\Unit\Http\Requests\Admin\UnitStoreRequest;
use Modules\Unit\Http\Requests\Admin\UnitUpdateRequest;

class UnitController extends Controller
{
  public function index()
  {
    $units = Unit::query()
      ->select(['id', 'name', 'status', 'created_at'])
      ->latest('id')
      ->get();

    return view('unit::admin.index', compact('units'));
  }

  public function store(UnitStoreRequest $request)
  {
    $unit = Unit::create($request->all());
    ActivityLogHelper::storeModel('واحد جدید ساخته شد.', $unit);

    return redirect()->back()->with('success', 'واحد با موفقیت ثبت شد.');
  }

  public function update(UnitUpdateRequest $request, Unit $unit)
  {
    $unit->update($request->all());
    ActivityLogHelper::updatedModel('واحد بروزرسانی شد.', $unit);

    return redirect()->back()->with('success', 'واحد با موفقیت به روزرسانی شد.');
  }

  public function destroy(Unit $unit)
  {
    $unit->delete();
    ActivityLogHelper::deletedModel('واحد حذف شد.', $unit);

    return redirect()->back()->with('success', 'واحد با موفقیت حذف شد.');
  }
}

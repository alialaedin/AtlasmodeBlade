<?php

namespace Modules\Advertise\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Advertise\Entities\PositionAdvertise;
use Modules\Advertise\Http\Requests\Position\PositionStoreRequest;
use Modules\Advertise\Http\Requests\Position\PositionUpdateRequest;
use Modules\Core\Helpers\Helpers;

class PositionAdvertiseController extends Controller
{
  public function index()
  {
    $positionAdvertiseBuilder = PositionAdvertise::with('advertisements');
    Helpers::applyFilters($positionAdvertiseBuilder);
    $positions = Helpers::paginateOrAll($positionAdvertiseBuilder);

    return view('advertise::admin.position.index', compact('positions'));
  }

  public function store(PositionStoreRequest $request)
  {
    $positionAdvertise = PositionAdvertise::create($request->all());
    $positionAdvertise->load('advertisements');
    ActivityLogHelper::simple('جایگاه ثبت شد', 'store', $positionAdvertise);

    return redirect()->route('admin.positions.index')
      ->with('success', 'جایگاه با موفقیت ثبت شد.');
  }

  public function update(PositionUpdateRequest $request, $positionAdvertiseId)
  {
    $positionAdvertise = PositionAdvertise::findOrFail($positionAdvertiseId);
    $positionAdvertise->update($request->all());
    $positionAdvertise->load('advertisements');
    ActivityLogHelper::updatedModel('جایگاه ویرایش شد', $positionAdvertise);

    return redirect()->route('admin.positions.index')
      ->with('success', 'جایگاه به روزرسانی شد.');
  }
}

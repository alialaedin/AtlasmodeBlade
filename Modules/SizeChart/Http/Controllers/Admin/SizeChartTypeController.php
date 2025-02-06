<?php

namespace Modules\SizeChart\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SizeChart\Entities\SizeChartType;
use Modules\SizeChart\Entities\SizeChartTypeValue;

class SizeChartTypeController extends Controller
{
  public function index()
  {
    $sizeChartTypes = SizeChartType::latest()->with('creator')->paginate()->withQueryString();

    return view('sizechart::admin.index', compact('sizeChartTypes'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name'  => 'required|string|unique:size_chart_types,name',
      'values' => 'required|array',
      'values.*' => 'required|string|min:1'
    ]);

    $sizeChartType = SizeChartType::query()->create(['name' => $request->name]);
    foreach ($request->values as $value) {
      $sizeChartTypeValue = new SizeChartTypeValue([
        'name' => $value
      ]);
      $sizeChartTypeValue->type()->associate($sizeChartType)->save();
    }

    return redirect()->back()->with('success', 'سایز چارت با موفقیت ایجاد شد');
  }

  public function update(Request $request, SizeChartType $sizeChartType)
  {
    $request->validate([
      'name'  => 'required|string|unique:size_chart_types,name,' . $sizeChartType->id,
      'values' => 'required|array',
      'values.*' => 'required|string|min:1'
    ]);

    $sizeChartType->update(['name' => $request->name]);
    $sizeChartType->values()->delete();

    foreach ($request->values as $value) {
      $sizeChartTypeValue = new SizeChartTypeValue([
        'name' => $value
      ]);
      $sizeChartTypeValue->type()->associate($sizeChartType)->save();
    }

    return redirect()->back()->with('success', 'سایز چارت با موفقیت ویرایش شد');
  }

  public function destroy(SizeChartType $sizeChartType)
  {
    $sizeChartType->delete();
    return redirect()->back()->with('success', 'سایز چارت با موفقیت حذف شد');
  }
}

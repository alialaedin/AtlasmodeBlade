<?php

namespace Modules\SizeChart\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\SizeChart\Entities\SizeChart;
use Modules\SizeChart\Http\Requests\Admin\StoreSizeChartRequest;
use Modules\SizeChart\Http\Requests\Admin\UpdateSizeChartRequest;

class SizeChartController extends Controller
{
  public function index()
  {
    $sizeCharts = SizeChart::query()
      ->select(['id', 'type_id', 'title', 'chart', 'created_at', 'product_id'])
      ->with([
        'type' => fn ($q) => $q->select(['id', 'name']),
        'product' => fn ($q) => $q->select(['id', 'title']),
      ])
      ->latest()
      ->filters()
      ->paginate()
      ->withQueryString();
      // dd(json_decode($sizeCharts->first()->chart));

    return view('sizechart::admin.index', compact('sizeCharts'));
  }

  /**
   * Store a newly created resource in storage.
   * @param StoreSizeChartRequest $request
   * @return JsonResponse
   */
  public function store(StoreSizeChartRequest $request)
  {
    $sizeChart = new SizeChart($request->all());
    $sizeChart->type()->associate($request->type_id)->save();

    return response()->success(' سایز چارت با موفقیت ایجاد شد.', compact('sizeChart'));
  }

  /**
   * Show the specified resource.
   * @param SizeChart $sizeChart
   * @return JsonResponse
   */
  public function show(SizeChart $sizeChart)
  {
    return response()->success('', compact('sizeChart'));
  }

  /**
   * Update the specified resource in storage.
   * @param Request $request
   * @param int $id
   * @return JsonResponse
   */
  public function update(UpdateSizeChartRequest $request, SizeChart $sizeChart)
  {
    $data = $request->toArray();
    $request->merge([
      'chart' => is_string($data['chart']) ? $data['chart'] : json_encode($data['chart'])
    ]);
    $sizeChart->update($request->all());

    return response()->success('سایز چارت با موفقیت بروزرسانی شد', compact('sizeChart'));
  }

  /**
   * Remove the specified resource from storage.
   * @param int $id
   * @return JsonResponse
   */
  public function destroy(SizeChart $sizeChart)
  {
    $sizeChart->delete();

    return response()->success('سایز چارت با موفقیت حذف شد', compact('sizeChart'));
  }
}

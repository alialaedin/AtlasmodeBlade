<?php

namespace Modules\Product\Http\Controllers\Admin;

use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Entities\Recommendation;
use Modules\Product\Entities\RecommendationGroup;
use Modules\Product\Http\Requests\Admin\RecommendationSortRequest;
use Modules\Product\Http\Requests\Admin\RecommendationStoreRequest;

class RecommendationController extends BaseController
{
  public function index(RecommendationGroup $recommendationGroup)
  {
    $recommendations = $recommendationGroup->items()
      ->orderByDesc('order')
      ->with('product:id,title')
      ->get();

    return view('product::admin.recommendation.index', compact(['recommendations', 'recommendationGroup']));
  }

  public function store(RecommendationStoreRequest $request)
  {
    $recommendation = Recommendation::create($request->validated());
    ActivityLogHelper::storeModel('گروه محصول پیشنهادی محصول ثبت شد', $recommendation);
    return redirect()->back()->with('success', 'محصول با موفقیت اضافه شد');
  }

  public function sort(RecommendationSortRequest $request)
  {
    Recommendation::sort($request);
    return redirect()->back()->with('success', 'مرتب سازی با موفقیت انجام شد');
  }

  public function destroy(Recommendation $recommendation)
  {
    $recommendation->delete();
    ActivityLogHelper::storeModel('محصول پیشنهادی حذف شد', $recommendation);
    return redirect()->back()->with('success', 'محصول از این گروه حذف شد');
  }
}

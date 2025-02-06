<?php

namespace Modules\Product\Http\Controllers\Admin;

use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Entities\Recommendation;
use Modules\Product\Http\Requests\Admin\RecommendationSortRequest;
use Modules\Product\Http\Requests\Admin\RecommendationStoreRequest;

class RecommendationController extends BaseController
{
  public function groups()
  {
    $groups = app(CoreSettings::class)->get('product.recommendation.groups');
    $allGroups = [];
    foreach ($groups as $group) {
      $allGroups[] = [
        "name" => $group,
        "label" => trans("core::groups.$group")
      ];
    }

    return view('product::admin.recommendation.groups', compact('allGroups'));
  }

  public function index($group)
  {
    $recommendations = Recommendation::query()
      ->orderByDesc('order')
      ->where('group', $group)
      ->with('product:id,title')
      ->get();

    return view('product::admin.recommendation.index', compact(['recommendations', 'group']));
  }

  public function store(RecommendationStoreRequest $request)
  {
    $recommendation = Recommendation::store($request);
    ActivityLogHelper::storeModel('گروه محصول پیشنهادی محصول ثبت شد', $recommendation);

    return redirect()->back()->with('success', 'محصول با موفقیت اضافه شد');
  }

  public function sort(RecommendationSortRequest $request, $group)
  {
    Recommendation::sort($request->ids, $group);

    return redirect()->back()->with('success', 'مرتب سازی با موفقیت انجام شد');
  }

  public function destroy(Recommendation $recommendation)
  {
    $recommendation->delete();
    ActivityLogHelper::storeModel('محصول پیشنهادی حذف شد', $recommendation);

    return redirect()->back()->with('success', 'محصول از این گروه حذف شد');
  }
}

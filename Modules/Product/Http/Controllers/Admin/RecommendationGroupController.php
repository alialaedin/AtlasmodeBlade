<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Product\Entities\RecommendationGroup;
use Modules\Product\Http\Requests\Admin\RecommendationGroupStoreRequest;
use Modules\Product\Http\Requests\Admin\RecommendationGroupUpdateRequest;

class RecommendationGroupController extends Controller 
{
	public function index()
	{
		$recommendationGroups = RecommendationGroup::getAllGroups();
		return view('product::admin.recommendation-group.index', compact('recommendationGroups'));
	}

	public function store(RecommendationGroupStoreRequest $request)
	{
		$group = RecommendationGroup::create($request->validated());
		$successMsg = 'گروه پیشنهادی با عنوان ' . $group->name . ' ایجاد شد';

		ActivityLogHelper::storeModel($successMsg, $group);
		return redirect()->back()->with('success', $successMsg);
	}

	public function update(RecommendationGroupUpdateRequest $request, RecommendationGroup $recommendationGroup)
	{
		$recommendationGroup->update($request->validated());
		$successMsg = 'گروه پیشنهادی با عنوان ' . $recommendationGroup->name . ' بروزرسانی شد';
		
		ActivityLogHelper::updatedModel($successMsg, $recommendationGroup);
		return redirect()->back()->with('success', $successMsg);
	}

	public function destroy(RecommendationGroup $recommendationGroup)
	{
		$recommendationGroup->delete();
		$successMsg = 'گروه پیشنهادی با عنوان ' . $recommendationGroup->name . ' حذف شد';
		
		ActivityLogHelper::deletedModel($successMsg, $recommendationGroup);
		return redirect()->back()->with('success', $successMsg);
	}
}

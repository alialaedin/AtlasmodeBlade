<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Product\Entities\RecommendationGroup;
use Modules\Product\Http\Requests\Admin\RecommendationGroupUpdateRequest;

class RecommendationGroupController extends Controller 
{
	public function index()
	{
		$recommendationGroups = RecommendationGroup::getAllGroups();
		return view('product::admin.recommendation-group.index', compact('recommendationGroups'));
	}

	public function update(RecommendationGroupUpdateRequest $request, RecommendationGroup $recommendationGroup)
	{
		$recommendationGroup->update($request->validated());
		$successMsg = 'گروه پیشنهادی با عنوان ' . $recommendationGroup->name . ' بروزرسانی شد';
		
		ActivityLogHelper::updatedModel($successMsg, $recommendationGroup);
		return redirect()->back()->with('success', $successMsg);
	}
}

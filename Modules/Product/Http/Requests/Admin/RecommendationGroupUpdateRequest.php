<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class RecommendationGroupUpdateRequest extends RecommendationGroupStoreRequest 
{
  public function rules()
	{
    $table = 'recommendation_groups';
    $id = $this->route('recommendationGroup');
    
		return [
			'name' => ['required', 'string', 'min:3', Rule::unique($table, 'name')->ignore($id)],
			'label' => ['required', 'string', 'min:3', Rule::unique($table, 'label')->ignore($id)],
			'show_in_home' => 'required|boolean',
			'show_in_filter' => 'required|boolean',
		];
	}
}
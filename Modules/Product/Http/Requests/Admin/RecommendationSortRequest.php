<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Recommendation;

class RecommendationSortRequest extends FormRequest
{

  public function rules()
  {
    return [
      'ids' => 'required|array',
      'ids.*' => 'required|integer|exists:recommendations,id',
      'group_id' => 'required|integer|exists:recommendation_groups,id'
    ];
  }

  protected function passedValidation()
  {
    $recommendationCount = Recommendation::query()->where('group_id', $this->group_id)->count();
    if ($recommendationCount != count($this->ids)) {
      throw Helpers::makeValidationException('شماسه های وارد شده نامعتبر است');
    }
  }
}

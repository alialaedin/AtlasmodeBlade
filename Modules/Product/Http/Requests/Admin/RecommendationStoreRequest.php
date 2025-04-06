<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Recommendation;

class RecommendationStoreRequest extends FormRequest
{
  public function rules()
  {
    return [
      'group_id' => 'required|integer|exists:recommendation_groups,id',
      'product_id' => 'required|integer|exists:products,id'
    ];
  }

  protected function passedValidation()
  {
    $existsProduct = Recommendation::query()
      ->where('group_id', $this->group_id)
      ->where('product_id', $this->product_id)
      ->exists();

    if ($existsProduct) {
      throw Helpers::makeValidationException('محصول مورد نظر قبلا به این گروه اضافه شده است');
    }
  }
}

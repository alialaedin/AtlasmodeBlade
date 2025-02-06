<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Recommendation;

class RecommendationStoreRequest extends FormRequest
{
  public function rules()
  {
    $groups = app(CoreSettings::class)->get('product.recommendation.groups');

    return [
      'group' => ['required', 'string', Rule::in($groups)],
      'product_id' => 'required|integer|exists:products,id'
    ];
  }

  protected function passedValidation()
  {
    $existsProduct = Recommendation::query()->where('group', $this->group)
      ->where('product_id', $this->product_id)->exists();

    if ($existsProduct) {
      throw Helpers::makeValidationException('محصول مورد نظر قبلا به این گروه اضافه شده است');
    }
  }
}

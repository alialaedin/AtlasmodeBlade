<?php

namespace Modules\SizeChart\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;

class UpdateSizeChartRequest extends FormRequest
{
  public function rules()
  {
    $title = Helpers::getModelIdOnPut('size_chart');
    return [
      'title' => 'required|unique:size_charts,title,' . $title,
      'chart' => 'required|array|present',
      'chart.*' => 'required|array|present',
      'chart.*.*' => 'required|present',
    ];
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }
}

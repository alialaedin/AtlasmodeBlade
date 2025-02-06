<?php

namespace Modules\SizeChart\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Classes\CoreSettings;

class StoreSizeChartRequest extends FormRequest
{
  public function rules(): array
  {
    $settings = app(CoreSettings::class);
    $haveType = $settings->get('size_chart.type')
      ? 'required|bail|integer|exists:size_chart_types,id'
      : 'nullable|bail|integer|exists:size_chart_types,id';

    return [
      'title' => 'bail|required|unique:size_charts,title',
      'type_id'  => $haveType,
      'chart' => 'required|array|present',
      'chart.*' => 'required|array|present',
      'chart.*.*' => 'required|present',
    ];
  }

  protected function passedValidation() {}
}

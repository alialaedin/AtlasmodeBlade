<?php


namespace Modules\Product\Entities;


use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\Attribute\Entities\AttributeValue;


class VarietyAttributeValuePivot extends Pivot
{
  protected $table = 'variety_attributes';
  protected $with = ['attributeValue'];

  public function attributeValue()
  {
    return $this->belongsTo(
      AttributeValue::class,
      'attribute_value_id',
      'id',
    );
  }
}

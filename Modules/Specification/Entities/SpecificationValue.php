<?php

namespace Modules\Specification\Entities;

use Illuminate\Database\Eloquent\Model;

class SpecificationValue extends Model
{
  protected $fillable = [
    'value',
    'selected'
  ];

  public static function booted()
  {
    static::deleting(function (SpecificationValue $specificationValue) {
      //check conditions
    });
  }

  public function specification()
  {
    return $this->belongsTo(Specification::class);
  }
}

<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasAuthors;

class AttributeValue extends Model
{
  use HasAuthors;

  protected $fillable = [
    'value',
    'selected'
  ];

  protected $hidden = ['created_at', 'updated_at', 'creator_id', 'updater_id'];

  public static function booted()
  {
    static::deleting(function (AttributeValue $attributeValueValue) {
      //check conditions
    });
  }
  public function attribute()
  {
    return $this->belongsTo(Attribute::class);
  }
}

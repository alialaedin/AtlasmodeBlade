<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class SpecificDiscountType extends Model
{
    protected $fillable = [
        'specific_discount_id',
        'discount_type',
        'discount',
    ];

    public const DISCOUNT_TYPE_FLAT = 'flat';
    public const DISCOUNT_TYPE_PERCENTAGE = 'percentage';
    
    public function getDiscountTypeLabelAttribute()
    {
        $arr = [
            self::DISCOUNT_TYPE_FLAT => 'مبلغ ثابت',
            self::DISCOUNT_TYPE_PERCENTAGE => 'درصد'
        ];

        return $arr[$this->discount_type];
    }

    public function specific_discount()
    {
        return $this->belongsTo(SpecificDiscount::class, 'specific_discount_id');
    }

    public function items()
    {
        return $this->hasMany(SpecificDiscountItem::class, 'specific_discount_type_id');
    }

}

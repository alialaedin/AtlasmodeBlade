<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class SpecificDiscount extends Model
{
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'creator_id',
        'updater_id',
        'done_at',
    ];

    public function types()
    {
        return $this->hasMany(SpecificDiscountType::class, 'specific_discount_id');
    }


    public static function is_deletable($id): bool
    {
        $specificDiscount = SpecificDiscount::findOrFail($id);
        if ($specificDiscount->start_date <= now() || $specificDiscount->done_at) {
            return false;
        }
        return true;
    }

    public function getIsDeletableAttribute(): bool
    {
        return static::is_deletable($this->id);
    }

    public function scopeFilters($query)
    {
        return $query
            ->when(request()->filled('start_date'),function($q){
                $q->where('created_at', '>=', request()->start_date);
            })
            ->when(request()->filled('end_date'),function($q) {
                $q->where('created_at', '<=', request()->end_date);
            });
    } 
}

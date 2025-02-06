<?php

namespace Modules\Store\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Variety;

class VarietyTransferItem extends Model
{

    protected $fillable = [
        'variety_transfer_id',
        'variety_id',
        'quantity',
    ];


    public function variety_transfer()
    {
        return $this->belongsTo(VarietyTransfer::class, 'variety_transfer_id');
    }

    public function variety()
    {
        return $this->belongsTo(Variety::class, 'variety_id');
    }


}

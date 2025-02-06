<?php

namespace Modules\Store\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Admin\Entities\Admin;
use Modules\Product\Entities\Variety;

class VarietyTransfer extends Model
{

    protected $fillable = ['from','to','mover','creator_id', 'is_delete', 'description', 'receiver', 'quantity', 'from_location_id', 'to_location_id'];
    protected $table = 'variety_transfers';

//    public function variety()
//    {
//        return $this->belongsTo(Variety::class, 'variety_id');
//    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'creator_id');
    }

    public function items()
    {
        return $this->hasMany(VarietyTransferItem::class, 'variety_transfer_id');
    }

    public function from_location()
    {
        return $this->belongsTo(VarietyTransferLocation::class, 'from_location_id');
    }

    public function to_location()
    {
        return $this->belongsTo(VarietyTransferLocation::class, 'to_location_id');
    }

}

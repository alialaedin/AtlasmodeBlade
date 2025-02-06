<?php

namespace Modules\Store\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VarietyTransferLocation extends Model
{
    protected $fillable = ['title', 'is_delete'];
    protected $hidden = ['created_at', 'updated_at'];

}

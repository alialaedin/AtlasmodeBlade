<?php

namespace Modules\Customer\Entities;

use Modules\Core\Entities\BaseModel;
use Modules\Shipping\Entities\Shipping;

class CustomerRole extends BaseModel
{
  protected $fillable = ['see_expired', 'name'];

  public function customers()
  {
    return $this->hasMany(\Modules\Customer\Entities\Customer::class, 'role_id');
  }

  public function shippings()
  {
    return $this->belongsToMany(Shipping::class)->withPivot(['amount']);
  }

  public function getIsDeletableAttribute()
  {
    return $this->customers->isEmpty();  
  }

}

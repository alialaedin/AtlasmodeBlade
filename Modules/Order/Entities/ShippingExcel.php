<?php

namespace Modules\Order\Entities;

use Modules\Core\Entities\BaseModel;

class ShippingExcel extends BaseModel
{
  protected $fillable = [
    'title',
    'barcode',
    'repository',
    'register_date',
    'special_services',
    'destination',
    'reference_number',
    'receiver_name',
    'sender_name',
    'price',
  ];
  
  public function scopeFilters($query)
  {
    return $query
      ->when(request('title'), function ($q) {
        $q->where('title', 'LIKE', '%' . request('title') . '%');
      })
      ->when(request('barcode'), function ($q) {
        $q->where('barcode', request('barcode'));
      })
      ->when(request('receiver_name'), function ($q) {
        $q->where('receiver_name', 'LIKE', '%' . request('receiver_name') . '%');
      });
  }
}

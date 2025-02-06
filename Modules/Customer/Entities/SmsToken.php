<?php

namespace Modules\Customer\Entities;

use Modules\Core\Entities\BaseModel;
use Modules\Core\Entities\HasCommonRelations;

class SmsToken extends BaseModel
{
  use HasCommonRelations;

  protected $fillable = [
    'mobile',
    'token',
    'expired_at',
    'verified_at'
  ];

  protected $dates = [
    'expired_at',
    'verified_at'
  ];
}

<?php

namespace Modules\Contact\Entities;

use Modules\Customer\Entities\Customer;
use Modules\Core\Entities\BaseModel as Model;

class Contact extends Model
{
  protected $fillable = [ 'name', 'phone_number', 'subject', 'body', 'status'];
  protected $with = ['customer'];

  public const CONTACT_URL = '/contact-us';
  public const ABOUT_URL = '/about-us';

  public function customer()
  {
    return $this->belongsTo(Customer::class)
      ->withOnly(['media'])
      ->select(['id', 'first_name', 'last_name', 'gender', 'mobile']);
  }
}

<?php

namespace Modules\Newsletters\Entities;

use Illuminate\Database\Eloquent\Model;

class UsersNewsletters extends Model
{
  protected $fillable = ['email', 'phone_number'];

  public static function getUsersEmails($sendForUsers = false): array
  {
    if ($sendForUsers) {
      #send For All Users
      $emails = static::query()->get()->pluck('email')->toArray();
    } else {
      #send For selected Users
      $emails = static::query()
        ->whereIn('id', $sendForUsers)
        ->get()->pluck('email')->toArray();
    }

    return $emails;
  }

  public function scopeFilters($query)
  {
    return $query;
  }
}

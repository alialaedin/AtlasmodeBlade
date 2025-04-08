<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Contracts\Notifiable as CoreNotifiable;
use Shetabit\Shopit\Modules\Admin\Database\factories\AdminFactory;
use Spatie\Permission\Traits\HasRoles;


/**
 * @method static find(int $int)
 */
class Admin extends Authenticatable implements CoreNotifiable
{
  use HasFactory, HasApiTokens, HasRoles, Notifiable;

  protected $guard_name = 'admin';

  protected $fillable = [
    'name',
    'username',
    'password',
    'email',
    'mobile',
  ];

  protected $appends = ['role'];

  protected $hidden = ['roles', 'updater', 'password', 'remember_token'];

  protected static function booted()
  {
    parent::booted();
    static::updating(function ($admin) {
      if (auth()->user() && !auth()->user()->hasRole('super_admin') && $admin->hasRole('super_admin')) {
        throw Helpers::makeValidationException('شما مجاز به ویرایش سوپر ادمین نمیباشید');
      }
    });
    static::deleted(function (Admin $admin) {
      $admin->tokens()->delete();
    });
  }

  public function setPasswordAttribute($value)
  {
    if ($value != null) {
      $this->attributes['password'] = bcrypt($value);
    }
  }

  public function getRoleAttribute()
  {
    $roles = $this->roles;
    if (empty($roles)) {
      return null;
    }
    return  $roles->first();
  }

  public function isSuperAdmin()
  {
    return $this->hasRole('super_admin');
  }

  public static function newFactory()
  {
    return AdminFactory::new();
  }
}

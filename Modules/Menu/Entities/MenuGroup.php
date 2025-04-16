<?php

namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MenuGroup extends Model
{
  protected $fillable = ['title'];

  public static function getAllMenuGroups()
  {
    return Cache::rememberForever('allMenuGroups', function () {
      return self::all(['id', 'title']);
    }); 
  }

  public function items()
  {
    return $this->hasMany(MenuItem::class, 'group_id');
  }
}

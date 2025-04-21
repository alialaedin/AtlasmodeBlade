<?php

namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MenuGroup extends Model
{
  protected $fillable = ['title'];
  protected $appends = ['label'];

  public const MENU_GROUP_HEADER = 'header';
  public const MENU_GROUP_FOOTER = 'footer';

  public static function registerMenuGroups()
  {
    DB::table(self::getTable())->insert([
      ['title' => self::MENU_GROUP_HEADER],
      ['title' => self::MENU_GROUP_FOOTER]
    ]);
  }

  public static function getAllMenuGroups()
  {
    return Cache::rememberForever('allMenuGroups', function () {
      return self::all(['id', 'title']);
    }); 
  }

  public function getLabelAttribute()
  {
    return config('menu.menuGroupLabels.' . $this->title);
  }

  public function items()
  {
    return $this->hasMany(MenuItem::class, 'group_id');
  }
}

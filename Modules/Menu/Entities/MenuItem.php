<?php

namespace Modules\Menu\Entities;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Admin\Entities\Admin;
use Modules\Core\Classes\DontAppend;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Core\Transformers\MediaResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Link\Traits\HasLinks;
use Spatie\MediaLibrary\HasMedia;

class MenuItem extends BaseModel implements HasMedia
{
  use HasLinks, InteractsWithMedia;

  protected $fillable = [
    'link',
    'title',
    'parent_id',
    'order',
    'new_tab',
    'status',
    'group_id'
  ];

  protected $hidden = ['media'];
  protected $appends = ['unique_type', 'icon', 'link_url'];

  private const ADMIN_FOOTER_MENUS_CACHE_KEY = 'admin_footer_menus';
  private const ADMIN_HAEDER_MENUS_CACHE_KEY = 'admin_header_menus';
  private const FRONT_ALL_MENUS_CACHE_KEY = 'front_menus';

  public static function booted()
  {
    static::deleting(function (self $menuItem) {
      $menuItem->children?->each(fn(self $childMenu) => $childMenu->delete());
    });

    static::created(fn(self $menuItem) => self::forgetCachesByGroup($menuItem->group));
    static::updated(fn(self $menuItem) => self::forgetCachesByGroup($menuItem->group));
    static::deleted(fn(self $menuItem) => self::forgetCachesByGroup($menuItem->group));
  }

  private static function forgetCachesByGroup(MenuGroup $menuGroup)
  {
    $adminCacheKey = $menuGroup->title === MenuGroup::MENU_GROUP_HEADER
      ? self::ADMIN_HAEDER_MENUS_CACHE_KEY 
      : self::ADMIN_FOOTER_MENUS_CACHE_KEY;

    Cache::forget($adminCacheKey);
    Cache::forget(self::FRONT_ALL_MENUS_CACHE_KEY);
  }

  private static function getCacheNameByGroup(MenuGroup $menuGroup)
  {
    return $menuGroup->title === MenuGroup::MENU_GROUP_HEADER
      ? self::ADMIN_HAEDER_MENUS_CACHE_KEY
      : self::ADMIN_FOOTER_MENUS_CACHE_KEY;
  }

  public static function getMenusForAdmin(MenuGroup $menuGroup): mixed
  {
    $cacheName = self::getCacheNameByGroup($menuGroup);
    return Cache::rememberForever($cacheName, function () use ($menuGroup) {
      return self::query()
        ->where('group_id', $menuGroup->id)
        ->whereNull('parent_id')
        ->orderByDesc('order')
        ->with('children')
        ->get();
    });
  }

  public static function getMenusForFront()
  {
    return Cache::rememberForever(self::FRONT_ALL_MENUS_CACHE_KEY, function () {
      return self::query()
        ->parents()
        ->active()
        ->orderByDesc('order')
        ->with(['children', 'group'])
        ->get()
        ->groupBy('group.title');
    });
  }

  public static function getMenusForUpdateCreate(MenuGroup $menuGroup)
  {
    return self::where('group_id', $menuGroup->id)->get(['id', 'title']);
  }

  public static function storeOrUpdate(Request $request, self|null $menuItem = null)
  {
    if ($menuItem) {
      $menuItem->update($request->all());
      ActivityLogHelper::updatedModel('منو بروز شد', $menuItem);
    } else {
      $menuItem = menuItem::query()->create($request->all());
      ActivityLogHelper::storeModel('منو ثبت شد', $menuItem);
    }
    if ($request->hasFile('icon')) {
      $media = $menuItem->getMedia('icon');
      foreach ($media ?? [] as $singleMedia) {
        $singleMedia->delete();
      }
      $menuItem->addIcon($request->file('icon'));
    }

    return $menuItem;
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('icon')->singleFile();
  }

  public function addIcon($file)
  {
    return $this->addMedia($file)->toMediaCollection('icon');
  }

  public function getIconAttribute(): MediaResource|DontAppend|null
  {
    if (!$this->relationLoaded('media')) {
      return new DontAppend('getIconAttribute');
    }
    $media = $this->getFirstMedia('icon');
    if (!$media) {
      return null;
    }
    return new MediaResource($media);
  }

  public function group()
  {
    return $this->belongsTo(MenuGroup::class, 'group_id');
  }

  public function parent(): BelongsTo
  {
    return $this->belongsTo(static::class, 'parent_id', 'id');
  }

  public function children(): HasMany
  {
    $q = $this->hasMany(static::class, 'parent_id', 'id')
      ->orderByDesc('order')->with('children');
    if (!(Auth::user() instanceof Admin)) {
      $q->active();
    }

    return $q;
  }

  public function scopeActive($query)
  {
    return $query->where('status', true);
  }

  public function scopeParents($query)
  {
    return $query->whereNull('parent_id');
  }

  public static function scopeIsParent($query)
  {
    $query->whereNull('parent_id');
  }

  public function getUniqueTypeAttribute()
  {
    if (!$this->linkable_type) {
      return 'self_link';
    }
    if ($this->linkable_id) {
      return basename($this->linkable_type);
    } else {
      if (Str::contains($this->linkable_type, 'Custom')) {
        return 'Index' . explode('\\', $this->linkable_type)[1];
      }else {
        return 'Index' . basename($this->linkable_type);
      }
    }
  }

  public function getLinkUrlAttribute()
  {
    switch ($this->unique_type) {
      case 'IndexPost':
        return route('front.posts.index');
      case 'Post':
        return route('front.posts.show', $this->linkable_id);
      case 'IndexProduct':
        return route('front.products.index');
      case 'Product':
        return route('front.products.show', $this->linkable_id);
      case 'Category':
        return route('front.products.index', ['category_id' => $this->linkable_id]);
      case 'IndexAboutUs':
        return '/about-us';
      case 'IndexContactUs':
        return '/contact-us';
      default:
        return $this->link;
    }
  }

  public static function sort(array $menuItems, $parentId = null)
  {
    $order = 999999;
    foreach ($menuItems as $menuItemArr) {
      $menuItem = self::find($menuItemArr['id']);
      if (!$menuItem) {
        continue;
      }

      $menuItem->update([
        'order' => $order--,
        'parent_id' => $parentId,
      ]);

      if (!empty($menuItemArr['children'])) {
        self::sort($menuItemArr['children'],$menuItem->id);
      }
    }
  }
}

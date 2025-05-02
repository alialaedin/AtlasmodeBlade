<?php

namespace Modules\Slider\Entities;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Transformers\MediaResource;
use Modules\Link\Traits\HasLinks;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Modules\Core\Traits\InteractsWithMedia;

class Slider extends BaseModel implements HasMedia
{
  use InteractsWithMedia, HasLinks, SortableTrait, HasAuthors;

  public const GROUP_DESKTOP = 'desktop';
  public const GROUP_MOBILE = 'mobile';

  private const MOBILE_SLIDERS_CACHE_NAME = 'allMobileSliders';
  private const DESKTOP_SLIDERS_CACHE_NAME = 'allDestktopSliders';
  private const HOME_SLIDERS_CACHE_NAME = 'homeSliders';

  public $sortable = [
    'order_column_name' => 'order',
    'sort_when_creating' => true,
  ];

  protected $hidden = ['media'];
  protected $casts = ['status' => 'boolean'];
  protected $appends = ['group_label', 'image'];
  protected $fillable = ['title', 'description', 'group', 'link', 'status', 'custom_fields'];

  protected static function booted()
  {
    static::created(fn(self $slider) => self::forgetCacheFromGroupName($slider->group));
    static::updated(fn(self $slider) => self::forgetCacheFromGroupName($slider->group));
    static::deleted(fn(self $slider) => self::forgetCacheFromGroupName($slider->group));
  }

  private static function forgetCacheFromGroupName(string $groupName)
  {
    $adminCacheName = $groupName === self::GROUP_MOBILE
      ? self::MOBILE_SLIDERS_CACHE_NAME
      : self::DESKTOP_SLIDERS_CACHE_NAME;

    Cache::forget($adminCacheName);
    Cache::forget(self::HOME_SLIDERS_CACHE_NAME);
  }

  public static function getAvailableGroups()
  {
    return [self::GROUP_DESKTOP, self::GROUP_MOBILE];
  }

  public static function getAllSlidersByGroup($group)
  {
    $cacheName = $group === self::GROUP_DESKTOP ? self::DESKTOP_SLIDERS_CACHE_NAME : self::MOBILE_SLIDERS_CACHE_NAME;
    return Cache::rememberForever($cacheName, function () use ($group) {
      return self::query()->orderByDesc('order')->whereGroup($group)->get();
    });
  }

  public static function getAllSliderGroups()
  {
    return Cache::rememberForever('allSliderGroups', function () {
      $groups = [];
      foreach (self::getAvailableGroups() as $group) {
        $groups[] = (object) [
          'title' => $group,
          'label' => config('slider.groupLabels.' . $group)
        ];
      }
      return (object) $groups;
    });
  }

  public static function getAllSlidersForFront()
  {
    return Cache::rememberForever(self::HOME_SLIDERS_CACHE_NAME, function () {
      return self::query()
        ->active()
        ->orderByDesc('order')
        ->get()
        ->groupBy('group');
    });
  }

  public static function createOrUpdate(Request $request, Slider|null $slider = null)
  {
    if ($slider) {
      $slider->update($request->all());
      if ($request->hasFile('image')) {
        $media = $slider->getMedia('main');
        foreach ($media as $singleMedia) {
          $singleMedia->delete();
        }
        $slider->addImage($request->file('image'));
      }
      ActivityLogHelper::updatedModel('اسلایدر بروز شد', $slider);
    } else {
      $slider = Slider::query()->create($request->all());
      $slider->addImage($request->file('image'));
      ActivityLogHelper::storeModel('اسلایدر ثبت شد', $slider);
    }
  }

  public static function sort(Request $request)
  {
    $idsFromRequest = $request->input('orders');
    $c = 999999;
    foreach ($idsFromRequest as $id) {
      $slider = Slider::query()->find($id);
      $slider->order = $c--;
      $slider->save();
    }
  }

  public function getGroupLabelAttribute()
  {
    return config('slider.groupLabels.' . $this->group);
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('main')->singleFile();
  }

  public function getImageAttribute()
  {
    $media = $this->getMedia('main')->first();

    return new MediaResource($media);
  }

  public function addImage($image)
  {
    if (!$image) return;
    return $this->addMedia($image)->toMediaCollection('main');
  }

  public function scopeActive($query)
  {
    return $query->where('status', 1);
  }

  public function scopeWhereGroup($query, $group)
  {
    return $query->where('group', $group);
  }
}

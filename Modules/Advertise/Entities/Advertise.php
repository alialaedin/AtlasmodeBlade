<?php

namespace Modules\Advertise\Entities;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Contact\Entities\Contact;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Core\Transformers\MediaResource;
use Modules\Link\Traits\HasLinks;
use Spatie\MediaLibrary\HasMedia;

class Advertise extends BaseModel implements HasMedia
{
  use InteractsWithMedia, HasLinks;

  protected $table = 'advertisements';
  protected $fillable = ['link', 'new_tab', 'status', 'start', 'end'];
  protected $hidden = ['media'];
  protected $appends = ['picture_url'];

  public const ADVERTISE_DESKTOP_RIGHT = 'advertise_desktop_right';
  public const ADVERTISE_DESKTOP_LEFT = 'advertise_desktop_left';
  public const ADVERTISE_MOBILE_TOP = 'advertise_mobile_top';
  public const ADVERTISE_MOBILE_BOTTOM = 'advertise_mobile_bottom';

  private const FRONT_ADVERTISEMENTS_CACHE_KEY = 'front_advertisements';
  private const ADMIN_ADVERTISEMENTS_CACHE_KEY = 'admin_advertisements';

  protected static function booted()
  {
    static::updated(function () {
      Cache::forget(self::FRONT_ADVERTISEMENTS_CACHE_KEY);
      Cache::forget(self::ADMIN_ADVERTISEMENTS_CACHE_KEY);
    });
  }

  public static function getAllAdvertisementsKeys()
  {
    return [
      self::ADVERTISE_DESKTOP_RIGHT,
      self::ADVERTISE_DESKTOP_LEFT,
      self::ADVERTISE_MOBILE_TOP,
      self::ADVERTISE_MOBILE_BOTTOM,
    ];
  }

  public static function makeAdvertisementsData()
  {
    $advertisements = [];
    foreach (self::getAllAdvertisementsKeys() as $key) {
      $advertisements[] = [
        'key' => $key,
        'title' => config('advertise.advertisementsKeyLabels.' . $key),
        'link' => '/',
        'linkable_id' => null,
        'linkable_type' => null,
        'new_tab' => 1,
        'status' => 0,
        'start' => null,
        'end' => null,
        'created_at' => now(),
        'updated_at' => now()
      ];
    }
    DB::table('advertisements')->insert($advertisements);
  }

  public static function getAdvertisementsForAdmin()
  {
    return Cache::rememberForever(self::ADMIN_ADVERTISEMENTS_CACHE_KEY, function () {
      return self::all(['id', 'key', 'title', 'status', 'start', 'end', 'updated_at']);
    });
  }

  public static function getAdvertisementsForFront()
  {
    return Cache::rememberForever(self::FRONT_ADVERTISEMENTS_CACHE_KEY, function () {
      $advertisements = self::query()
        ->where('status', 1)
        ->whereNotNull('start')
        ->whereNotNull('end')
        ->whereDate('start', '<=', Carbon::now())
        ->whereDate('end', '>=', Carbon::now())
        ->get()
        ->mapWithKeys(function ($advertise) {
          $advertise->append('link_url');
          $advertise->makeHidden(['picture', 'unique_type']);
          return [$advertise->key => $advertise];
        });

      return $advertisements;
    });
  }

  public static function updateAdvertise(self $advertise, Request $request): void
  {
    Helpers::toCarbonRequest(['start', 'end'], $request);
    $advertise->update($request->except(['possibility', 'position_id']));

    if ($request->hasFile('picture')) {
      $media = $advertise->getMedia('picture');
      foreach ($media ?? [] as $singleMedia) {
        $singleMedia->delete();
      }
      $advertise->addPicture($request->file('picture'));
    }

    ActivityLogHelper::updatedModel('بنر تبلیغاتی بروزرسانی شد', $advertise);
  }

  public static function changeStatus(self $advertise)
  {
    $advertise->status = !$advertise->status;
    $advertise->save();
    ActivityLogHelper::updatedModel('بنر تبلیغاتی بروزرسانی شد', $advertise);
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('picture')->singleFile();
  }

  public function addPicture($file)
  {
    return $this->addMedia($file)->toMediaCollection('picture');
  }

  public function getPictureAttribute()
  {
    $media = $this->getFirstMedia('picture');
    if (!$media) {
      return null;
    }
    return new MediaResource($media);
  }

  public function getPictureUrlAttribute()
  {
    if (!$this->picture) return null;
    return '/storage/' . $this->picture->uuid . '/' . $this->picture->file_name;
  }

}

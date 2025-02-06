<?php

namespace Modules\Advertise\Entities;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Admin;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Link\Traits\HasLinks;
use Spatie\MediaLibrary\HasMedia;
use Modules\Advertise\Entities\PositionAdvertise;

class Advertise extends BaseModel implements HasMedia
{
  use HasFactory, InteractsWithMedia, HasLinks;

  protected $table = 'advertisements';
  protected $fillable = ['position_id', 'link', 'start', 'end', 'possibility'];
  protected $hidden = ['media'];
  protected $appends = ['picture', 'unique_type'];


  protected static function booted()
  {
    static::creating(function (Advertise $advertise) {
      if (($admin = Auth::user()) instanceof Admin) {
        $advertise->admin()->associate($admin);
      }
    });

    static::deleted(function (Advertise $advertise) {
      $possibility = $advertise->possibility;
      /**
       * @var PositionAdvertise $positionAdvertise
       */
      $positionAdvertise = $advertise->positionAdvertise()->first();
      /**
       * @var Advertise $firstAdvertise
       */
      $firstAdvertise = $positionAdvertise->advertisements()->where('id', '!=', $advertise->id)->first();
      if ($firstAdvertise) {
        $firstAdvertise->possibility += $possibility;
        $firstAdvertise->save();
      }
    });

    Helpers::clearCacheInBooted(static::class, 'home_advertise');
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }

  public function positionAdvertise()
  {
    return $this->belongsTo(PositionAdvertise::class, 'position_id');
  }

  public function scopeNotExpired($query)
  {
    return $query->where('end', '<', Carbon::now());
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
    return $media->getUrl();
  }

  public function getUniqueTypeAttribute()
  {
    if (!$this->linkable_type) {
      return 'link_url';
    }
    if ($this->linkable_id) {
      return $this->linkable_type;
    } else {
      return 'Index' . $this->linkable_type;
    }
  }

  public static function getAllGroupedBy()
  {
    return Helpers::cacheForever('home_advertise', function () {
      $positionAdvertises = PositionAdvertise::query()->with('advertisements')
        ->get();
      $advertisements = [];
      foreach ($positionAdvertises as $positionAdvertise) {
        $advertisements[$positionAdvertise->key] = [];
        foreach ($positionAdvertise->advertisements as $advertisement) {
          $advertisements[$positionAdvertise->key][] = $advertisement;
        }
      }
      return $advertisements;
    });
  }

  // Select random banner
  public static function getForHome()
  {
    $advertisementGroups = static::getAllGroupedBy();
    $finalAdvertisements = [];
    foreach ($advertisementGroups as $group => $advertisements) {
      if (!empty($advertisements)) {
        $finalAdvertisements[$group] = $advertisements[array_rand($advertisements)];
      }
    }
    return $finalAdvertisements;
  }
}

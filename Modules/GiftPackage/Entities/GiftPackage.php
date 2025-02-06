<?php

namespace Modules\GiftPackage\Entities;

use Modules\Core\Entities\BaseModel;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Core\Transformers\MediaResource;
use Modules\Order\Entities\Order;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;

class GiftPackage extends BaseModel implements Sortable, HasMedia
{
    use HasAuthors, SortableTrait, InteractsWithMedia;

    protected static $commonRelations = [
        
    ];
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => false,
    ];
    protected $fillable = [
        'name',
        'price',
        'order',
        'description',
        'status',
    ];
    protected $appends = ['image'];

    protected $hidden = ['media'];

    public static function booted()
    {
        static::deleting(function (GiftPackage $gift_package) {
            if ($gift_package->id == 1) {
                throw Helpers::makeValidationException('ادمین عزیز بسته بندی پیش فرض غیرقابل حذف میباشد.');
            }
        });
        static::deleting(function (GiftPackage $gift_package) {
            if ($gift_package->orders()->exists()) {
                throw Helpers::makeValidationException('به علت وجود سفارش برای این  بسته بندی هدیه, امکان حذف آن وجود ندارد');
            }
        });

    }

    public function scopeActive($query)
    {
        $query->where('status', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function addImage($file)
    {
        $media = $this->addMedia($file)
            ->withCustomProperties(['type' => 'gift_package'])
            ->toMediaCollection('image');
        $this->load('media');

        return $media;
    }

    //Custom

    public function getImageAttribute(): ?MediaResource
    {
        $media = $this->getFirstMedia('image');
        if (!$media) {
            return null;
        }
        return new MediaResource($media);
    }

    public function storeImage($request)
    {
        if ($request->hasFile('image')) {
            $this->addImage($request->image);
        }
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}

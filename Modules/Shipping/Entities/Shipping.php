<?php

namespace Modules\Shipping\Entities;

use Illuminate\Support\Facades\Auth;
use Modules\Order\Entities\Order;
use Modules\Area\Entities\City;
use Modules\Area\Entities\Province;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasAuthors;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Core\Transformers\MediaResource;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerRole;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;

class Shipping extends BaseModel implements Sortable, HasMedia
{
    use HasAuthors, SortableTrait, InteractsWithMedia;

    protected $fillable = [
        'minimum_delay',
        'name',
        'default_price',
        'free_threshold',
        'order',
        'description',
        'status',
        'packet_size',
        'first_packet_size',
        'more_packet_price',
        'is_free'
    ];

    protected static $commonRelations = [
        'provinces', 'cities', 'customerRoles'
    ];
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];
    protected $appends = ['logo'];

    protected $hidden = ['media'];

    public static function booted()
    {
        static::deleting(function (Shipping $shipping) {
            if ($shipping->orders()->exists()) {
                throw Helpers::makeValidationException('به علت وجود سفارش برای این روش ارسال امکان حذف آن وجود ندارد');
            }
        });
    }

    public function scopeActive($query)
    {
        $query->where('status', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function addImage($file)
    {
        $media = $this->addMedia($file)
            ->withCustomProperties(['type' => 'shipping'])
            ->toMediaCollection('logo');
        $this->load('media');

        return $media;
    }

    public function getPrice(City $city, int $orderAmount, $newQuantity, $morePacketPrice =null, $firstPacketSize=null)
    {
        if ($newQuantity == 0) {
            throw new \LogicException('Total quantity should by not zero');
        }

        if (!request('reserved') && $this->free_threshold && $orderAmount >= $this->free_threshold) {
            return 0;
        }

        $fromCustomer = $this->getForCustomerPrice(Auth::user());
        if ($fromCustomer !== false) {
            return $fromCustomer;
        }

        $price = $this->getAreaPrice($city, $orderAmount);

        return static::getPacketHelper($newQuantity,$this->packet_size, $price,
            $morePacketPrice ?? $this->more_packet_price, $firstPacketSize ?? $this->first_packet_size);
    }

    public function getForCustomerPrice(Customer $customer)
    {
        if (!($customer instanceof Customer)) {
            return false;
        }
        $coreSetting = app(CoreSettings::class);
        if (!$coreSetting->get('customer.has_role')) {
            return false;
        }
        /** @var CustomerRole $customerRole */
        $customerRole = $customer->role;
        if (!$customerRole) {
            return false;
        }
        $shipping = $customerRole->shippings()->where('shippings.id', $this->id)->first();
        if ($shipping) {
            return $shipping->pivot->amount;
        }
        return false;
    }

    public function checkShippableAddress(?City $city): bool
    {
        if ($city == null) {
            throw new \Exception('لطفا در آدرس خود یک شهر انتخاب کنید');
        }
        $shippableAddress = false;
        if ($this->cities->count() < 1 && $this->provinces->count() < 1) {
            $shippableAddress = true;
        } elseif ($this->cities->count() > 0) {
            $shippableAddress = $this->cities->contains('id', $city->id);
        } elseif ($this->provinces->count() > 0) {
            $shippableAddress = $this->provinces->contains('id', $city->province_id);
        }

        return $shippableAddress;
    }

    public function setProvinces($request)
    {
        $provinces = [];
        foreach ($request->provinces ?? [] as $province) {
            $provinces[$province['id']] = [
                'price' => $province['price'] ?? null
            ];
        }

        $this->provinces()->sync($provinces);
    }

    public function setCustomerRoles($request)
    {
        $customerRoles = [];
        foreach ($request->customer_roles ?? [] as $customerRole) {
            $customerRoles[$customerRole['id']] = [
                'amount' => $customerRole['amount'] ?? null
            ];
        }

        $this->customerRoles()->sync($customerRoles);
    }

    public function getPriceByReservation(City $city, int $orderAmount, $newQuantity, $customer, $addressId, int $except = null)
    {
        if ($newQuantity == 0) {
            throw new \LogicException('Total quantity should by not zero');
        }

        if ($this->free_threshold && $orderAmount >= $this->free_threshold) {
            return 0;
        }

        $orders = $customer->orders();
        $parentOrder = $orders
            ->where('address_id', $addressId)
            ->where('status', Order::STATUS_RESERVED)
            ->isReserved()
            ->latest()->first();

        $fromCustomer = $this->getForCustomerPrice(Auth::user());
        if ($fromCustomer !== false) {
            return $fromCustomer;
        }

        /** @var $parentOrder Order */
        if ($parentOrder) {
            $shippingPacketPrice = $parentOrder->shipping_packet_amount;
            $oldQuantity = $parentOrder->getTotalTotalQuantity();
            $oldShippingAmountPaid = $parentOrder->shipping_amount;

            return static::getPacketHelper($newQuantity,$this->packet_size,
            $shippingPacketPrice, $this->more_packet_price, $this->first_packet_size, $oldQuantity,$oldShippingAmountPaid);
        }

        $price = $this->getAreaPrice($city, $orderAmount);
        return static::getPacketHelper($newQuantity,$this->packet_size, $price, $this->more_packet_price, $this->first_packet_size);
    }

    public function getAreaPrice($city, $orderAmount)
    {
        $price = $this->attributes['default_price'];
        // برای رزور ها حد آستانه رایگان نداریم
        if ($this->free_threshold && $orderAmount >= $this->free_threshold) {
            return 0;
        }elseif ($shippableCity = $this->cities->where('id', $city->id)->first()) {
            $price = $shippableCity->pivot->price;
        } elseif ($shippableProvince = $this->provinces->where('id', $city->province_id)->first()) {
            $price = $shippableProvince->pivot->price;
        }
        return $price;
    }
    public static function getPacketHelper($quantity, $packetSize, $price, $morePrice,$first_packet_size, $oldQuantity = 0, $oldShippingAmountPaid = 0)
    {
        $allQuantity = $quantity + $oldQuantity;
        if ($allQuantity <= $first_packet_size){
            return $price - $oldShippingAmountPaid;
        }
        $newQuantity = $allQuantity - $first_packet_size;

        $totalPackets = (int)ceil($newQuantity / $packetSize);
        return (int)($price + ($totalPackets) * $morePrice) - $oldShippingAmountPaid;
    }

    public static function getShippableShippingsForAddress($address)
    {
        $suitableShippings = [];
        $allShippings = static::query()->active()->get();
        foreach ($allShippings as $shipping) {
            if ($shipping->checkShippableAddress($address->city))
                $suitableShippings[] = $shipping;
            $address->unsetRelation('city');
        }

        foreach ($suitableShippings as $suitableShipping) {
            $suitableShipping->makeHidden(['cities','provinces','shippingRanges']);
        }
        return $suitableShippings;
    }
    

    public function getIsPublicAttribute(): bool
	{
		return $this->provinces->isEmpty() && $this->cities->isEmpty();
	}

    public function getLogoAttribute(): ?MediaResource
    {
        $media = $this->getFirstMedia('logo');
        if (!$media) {
            return null;
        }
        return new MediaResource($media);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customerRoles()
    {
        return $this->belongsToMany(CustomerRole::class)->withPivot(['amount']);
    }

    public function provinces()
    {
        return $this->morphedByMany(Province::class, 'shippable')->active()->withPivot(['price']);
    }

    public function cities()
    {
        return $this->morphedByMany(City::class, 'shippable')->active()->withPivot(['price']);
    }
}

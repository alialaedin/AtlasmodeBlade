<?php

namespace Modules\Customer\Entities;

use Modules\Area\Entities\City;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Entities\HasCommonRelations;
use Modules\Core\Exceptions\ModelCannotBeDeletedException;
use Modules\Core\Traits\HasAuthors;
use Modules\Order\Entities\Order;
use Modules\Customer\Entities\Customer;

class Address extends BaseModel
{
	use HasAuthors, HasCommonRelations;

	protected $fillable = [
		'city_id',
		'first_name',
		'last_name',
		'mobile',
		'address',
		'postal_code',
		'telephone',
		'latitude',
		'longitude'
	];

	protected $with = ['city.province'];

	protected static function booted(): void{
		static::deleting(function (self $address) {
			if ($address->orders->isNotEmpty()) {
				throw new ModelCannotBeDeletedException('آدرس به سفارشی وصل است و قابل حذف نمی باشد');
			}
		});
	} 

	//Relations

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function city()
	{
		return $this->belongsTo(City::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}
}

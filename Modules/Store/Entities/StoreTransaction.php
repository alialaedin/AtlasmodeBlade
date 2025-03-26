<?php

namespace Modules\Store\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\HasMorphAuthors;
use Modules\Order\Entities\Order;

class StoreTransaction extends Model
{
	use HasMorphAuthors;

	public $with = ['creator'];
	protected $fillable = [
		'type',
		'description',
		'quantity',
		'mini_order_id',
		'order_id',
		'is_done'
	];

	public const TYPE_DECREMENT = 'decrement';
	public const TYPE_INCREMENT = 'increment';

	public static function getAvailableTypes()
	{
		return [self::TYPE_DECREMENT, self::TYPE_DECREMENT];
	}

	public function scopeFilters($query)
	{
		return $query
			->when(request('id'), fn ($q) => $q->where('id', request('id')))
			->when(request('type'), fn ($q) => $q->where('type', request('type')))
			->when(request('start_date'), fn ($q) => $q->whereDate('created_at', '>=', request('start_date')))
			->when(request('end_date'), fn ($q) => $q->whereDate('created_at', '<=', request('end_date')));
	} 

	public function order(): BelongsTo
	{
		return $this->belongsTo(Order::class, 'order_id');
	}

	public function store()
	{
		return $this->belongsTo(Store::class);
	}

	public function miniOrder()
	{
		return $this->belongsTo(Store::class);
	}
}

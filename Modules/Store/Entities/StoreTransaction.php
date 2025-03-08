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

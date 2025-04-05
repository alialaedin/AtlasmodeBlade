<?php

namespace Modules\Order\Entities;

use Modules\Core\Helpers\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Modules\Order\Services\Statuses\ChangeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Entities\Admin;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Traits\HasMorphAuthors;
use Modules\Flash\Entities\Flash;
use Modules\Product\Entities\Gift;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variety;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Order\Entities\Order;

class OrderItem extends BaseModel
{
	use HasMorphAuthors, LogsActivity;

	protected static $recordEvents = ['deleted', 'updated'];

	protected $fillable = [
		'product_id',
		'variety_id',
		'quantity',
		'amount',
		'status',
		'discount_amount',
		'extra',
		'flash_id',
		'is_done'
	];

	public static function booted()
	{
		static::deleting(fn(self $orderItem) => $orderItem->orderItemLogs()->delete());
		static::deleted(fn(self $orderItem) => $orderItem->gifts()->detach());
	}

	public function getActivitylogOptions(): LogOptions
	{
		$user = auth()->user();
		$name = $user instanceof Admin ? $user->username : $user?->mobile;
		return LogOptions::defaults()
			->useLogName('OrderItems')
			->logAll()
			->logOnlyDirty()
			->setDescriptionForEvent(function ($eventName) use ($name) {
				$eventName = Helpers::setEventNameForLog($eventName);
				return "آیتم سفارش با شناسه {$this->id} توسط {$name} {$eventName} شد.";
			});
	}

	public function scopeActive($query)
	{
		$query->where($this->getTable() . '.status', 1);
	}

	public function scopeDoneState(Builder $orderItem_query, $preferred_done_state)
	{
		return $orderItem_query->when(!$preferred_done_state, function (Builder $query) use ($orderItem_query) {
			$orderItem_query->where('is_done', false)->orWhereNull('is_done');
		}, $orderItem_query->where('is_done', $preferred_done_state));
	}

	public function scopePendingOrderItems(Builder $query): Builder
	{
		$request = request();
		Helpers::toCarbonRequest(['start_date', 'end_date'], $request);

		return $query
			->whereHas('order', function ($order_query) {
				$order_query->whereNotIn('status', array_merge(['delivered'], ChangeStatus::FAILED_STATUS));
			})
			->where(function ($q) {
				$q->where('is_done', false)->orWhereNull('is_done');
			})
			->when($request->filled('start_date'), function (Builder $q) use ($request) {
				$q->where('order_items.created_at', '>', $request->start_date);
			})
			->when($request->filled('end_date'), function (Builder $q) use ($request) {
				$q->where('order_items.created_at', '<', $request->end_date);
			});
	}

	public function scopeDoneOrderItems(Builder $query): Builder
	{
		$request = request();
		Helpers::toCarbonRequest(['start_date', 'end_date'], $request);

		return $query
			->whereHas('order', function ($order_query) {
				$order_query->whereNotIn('status', array_merge(['delivered'], ChangeStatus::FAILED_STATUS));
			})
			->where(function ($q) {
				$q->where('is_done', true);
			})
			->when($request->filled('start_date'), function (Builder $q) use ($request) {
				$q->where('order_items.created_at', '>', $request->start_date);
			})
			->when($request->filled('end_date'), function (Builder $q) use ($request) {
				$q->where('order_items.created_at', '<', $request->end_date);
			});
	}

	//Relations
	public function order(): BelongsTo
	{
		return $this->belongsTo(Order::class);
	}

	public function product(): BelongsTo
	{
		return $this->belongsTo(Product::class);
	}

	public function variety(): BelongsTo
	{
		return $this->belongsTo(Variety::class);
	}

	public function flash(): BelongsTo
	{
		return $this->belongsTo(Flash::class);
	}

	public function gifts(): belongsToMany
	{
		return $this->belongsToMany(Gift::class, 'gift_order_item')->active();
	}

	public function orderItemLogs()
	{
		return $this->hasMany(OrderItemLog::class);
	}
}

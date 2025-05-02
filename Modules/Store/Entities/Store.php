<?php

namespace Modules\Store\Entities;

use Exception;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variety;
use Modules\Product\Jobs\SendProductAvailableNotificationJob;
use Illuminate\Database\Eloquent\Model;
use Modules\Store\Jobs\ProductUnavailableNotificationJob;
use Modules\Store\Entities\StoreTransaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Core\Helpers\Helpers;

class Store extends Model
{
	const TYPE_INCREMENT = 'increment';
	const TYPE_DECREMENT = 'decrement';

	protected $fillable = ['balance', 'variety_id'];

	protected static function booted()
	{
		static::updated(function (Store $store) {
			if ($store->balance == 0) {
				$siblingIds = $store->variety->product->varieties
					->where('variety_id', '!=', $store->variety_id)
					->pluck('id')
					->all();
				if (count($siblingIds) == 0 || Store::whereIn('variety_id', $siblingIds)->where('balance', '>', 0)->count() == 0) {
					$product = $store->variety->product;
					$product->update([
						'status' => Product::STATUS_OUT_OF_STOCK,
					]);
					ProductUnavailableNotificationJob::dispatch($product);
				}
			}
			if ($store->isDirty('balance')) {
				Cache::forget('variety-quantity-' . $store->variety->id);
			}
		});
	}

	public static function insertModel(object $request)
	{
		DB::beginTransaction();
		try {

			$variety = Variety::find($request->variety_id);
			$storeQuery = $variety->store();
			$store = $storeQuery->exists() ? $storeQuery->first() : $storeQuery->create(['balance' => 0]);
			$oldBalance = $store->balance ?? 0;

			if ($request->type == self::TYPE_DECREMENT && $store->balance < $request->quantity) {
				$varietyTitle = $store->variety->title;
				throw Helpers::makeValidationException("موجودی محصول $varietyTitle کمتر از $request->quantity عدد است.");
			}

			$data = [
				'quantity' => (int)$request->quantity,
				'description' => $request->description,
				'type' => $request->type,
				'store_id' => $store->id,
				'mini_order_id' => $request->mini_order_id ?? null,
				'order_id' => $request->order_id ?? null,
				'created_at' => now(),
				'updated_at' => now(),
			];

			$store->{$request->type}('balance', $request->quantity);
			$store->transactions()->create($data);

			if ($oldBalance == 0 && $variety->store->balance > 0 && $variety->product->status == Product::STATUS_AVAILABLE) {
				SendProductAvailableNotificationJob::dispatch($variety);
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			throw $e;
		}
	}

	public static function getAvailableTypes()
	{
		return [self::TYPE_INCREMENT, self::TYPE_DECREMENT];
	}

	public function scopeFilters($query)
	{
		$storeId = request('id');
		$productId = request('product_id');
		$varietyId = request('variety_id');
		$type = request('type');
		$startDate = request('start_date');
		$endDate = request('end_date') ?? now();

		return $query
			->when($storeId, fn($q) => $q->where('id', $storeId))
			->when($productId, fn($q) => $q->whereIn('variety_id', Product::getActiveVarietiyIds($productId)))
			->when($varietyId, fn($q) => $q->where('variety_id', $varietyId))
			->when($type, fn($q) => $q->whereHas('transactions', fn($tq) => $tq->where('type', $type)))
			->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
			->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
	}

	public function variety(): BelongsTo
	{
		return $this->belongsTo(Variety::class, 'variety_id')->with('product');
	}

	public function transactions(): HasMany
	{
		return $this->hasMany(StoreTransaction::class, 'store_id');
	}
}

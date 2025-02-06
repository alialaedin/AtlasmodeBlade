<?php

namespace Modules\Store\Entities;

use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variety;
use Modules\Product\Jobs\SendProductAvailableNotificationJob;
use Modules\Core\Entities\BaseModel;
use Exception;
use Modules\Store\Jobs\ProductUnavailableNotificationJob;
use Modules\Store\Entities\StoreTransaction;
use Shetabit\Shopit\Modules\Store\Database\factories\StoreFactory;

class Store extends BaseModel
{
    const TYPE_INCREMENT = 'increment';
    const TYPE_DECREMENT = 'decrement';

    public static $commonRelations = [
        'variety'
    ];

    protected $fillable = [
        'balance',
        'variety_id'
    ];

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
               \Cache::forget('variety-quantity-' . $store->variety->id);
            }
        });
    }

    protected static function newFactory()
    {
        return StoreFactory::new();
    }

    public static function getAvailableTypes()
    {
        return [self::TYPE_INCREMENT, self::TYPE_DECREMENT];
    }

    public function variety(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Variety::class, 'variety_id')->with('product');
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StoreTransaction::class, 'store_id');
    }
    
    public static function insertModel($request, $forced = false): ?Store
    {
        if (!$request || (request('product.no_store_update') && !$forced)) {
            return null;
        }
        $variety = Variety::query()->find($request->variety_id);
        $oldBalance = $variety?->store?->balance ?? null;

        if (!$variety) {
            throw new Exception('به علت حذف یکی از تنوع ها امکان این عمل وجود ندارد');
        }
        $store = $variety->store()->exists() ? $variety->store : $variety->store();

        if (! $store->exists()) {
            $store = $store->create([
                'balance' => 0
            ]);
        }

        if ($request->type == self::TYPE_DECREMENT && $store->balance < $request->quantity){
            $variety->load('attributes');
            throw new Exception("موجودی محصول " . $variety->title ." کمتر از {$request->quantity} عدد است.", '422');
        }

        /** @var $store Store */
        $data = [
            "type" => $request->type,
            "description" => $request->description,
            "quantity" => $request->quantity,
            'order_id' => $request->order_id ?? null
        ];
        if (isset($request->mini_order_id) && $request->mini_order_id) {
            $data['mini_order_id'] = $request->mini_order_id;
        }
        $transaction = $store->transactions()->create($data);

        $method = $transaction->type; // increment , decrement
        $store->$method('balance', $transaction->quantity);
        //example $store->increment('balance', $transaction->quantity);
        //example $store->decrement('balance', $transaction->quantity);

        if ($oldBalance == 0 && $variety?->store?->balance > 0 && $variety->product->status == Product::STATUS_AVAILABLE){
            SendProductAvailableNotificationJob::dispatchNow($variety);
        }

        return $store;
    }

    public function scopeFilters($query) {
        return $query
            ->when(request('id'), function ($query) {
                $query->where('id', request('id'));
            })
            ->when(request('product_id'), function ($query) {
                $query->whereIn('variety_id', Product::getActiveVarietiyIds(request('product_id')));
            })
            ->when(request('variety_id'), function ($query) {
                $query->where('variety_id', request('variety_id'));
            })
            ->when(request('type'), function ($query) {
                $query->whereHas('transactions', function ($query) {
                    $query->where('type', request('type'));
                });
            })
            ->when(request('start_date'), function ($query) {
                $query->whereDate('created_at', '>=', request('start_date'));
            })
            ->when(request('end_date'), function ($query) {
                $query->whereDate('created_at', '<=', request('end_date'));
            });
    }

}

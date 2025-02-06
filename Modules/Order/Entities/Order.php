<?php

namespace Modules\Order\Entities;

use Illuminate\Support\Facades\Mail;
use Modules\Core\Classes\CoreSettings;
use Modules\Coupon\Entities\Coupon;
use Modules\Order\Mail\NewOrderEmail;
use Modules\GiftPackage\Entities\GiftPackage;
use Bavix\Wallet\Traits\HasWallet;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Admin;
use Modules\Cart\Entities\Cart;
use Modules\Core\Entities\HasCommonRelations;
use Modules\Core\Entities\HasFilters;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasMorphAuthors;
use Modules\Customer\Entities\Address;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Notifications\InvoicePaid;
use Modules\Invoice\Classes\Payable;
use Modules\Invoice\Entities\Invoice;
use Modules\Invoice\Entities\Payment;
use Modules\Order\Classes\OrderStoreProperties;
use Modules\Order\Entities\OrderStatusLog;
use Modules\Order\Jobs\NewOrderForCustomerNotificationJob;
use Modules\Product\Entities\Variety;
use Modules\Setting\Entities\Setting;
use Modules\Shipping\Entities\Shipping;
use Bavix\Wallet\Interfaces\Product as ProductWallet;
use Bavix\Wallet\Interfaces\Customer as CustomerWallet;
use Modules\Store\Entities\Store;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Entities\OrderLog;

class Order extends Payable implements ProductWallet
{
	public static $commonRelations = [
		'customer',
		'statusLogs',
		'items',
		'invoices.payments',
		'shipping',
		'orderLogs',
		'gift_package'
	];
	protected $fillable = [
		'shipping_id',
		'coupon_id',
		'address',
		'address_id',
		'shipping_amount',
		'discount_amount',
		'description',
		'status',
		'status_detail',
		'delivered_at',
		'reserved',
		'shipping_packet_amount',
		'shipping_more_packet_price',
		'shipping_first_packet_size',
		'gift_package_id',
		'gift_package_price'
	];

	use HasFactory, HasCommonRelations, HasMorphAuthors, HasFilters, HasWallet, LogsActivity;

	const STATUS_NEW = 'new';
	const STATUS_WAIT_FOR_PAYMENT = 'wait_for_payment';
	const STATUS_IN_PROGRESS = 'in_progress';
	const STATUS_DELIVERED = 'delivered';
	const STATUS_CANCELED = 'canceled';
	const STATUS_FAILED = 'failed';
	const STATUS_RESERVED = 'reserved';

	const ACTIVE_STATUSES = [
		Order::STATUS_DELIVERED,
		Order::STATUS_IN_PROGRESS,
		Order::STATUS_RESERVED,
		Order::STATUS_NEW
	];

	protected $appends = ['total_amount'];
	protected $casts = [
		'shipping_amount' => 'integer',
		'discount_amount' => 'integer'
	];

	protected $payDescription = null;

	public static function booted()
	{
		static::deleting(function (\Modules\Order\Entities\Order $order) {
			$order->items->each(fn($item) => $item->delete());
			$order->reservations->each(fn($_order) => $_order->delete());
			$order->orderLogs->each(fn($_orderLog) => $_orderLog->delete());
		});
	}

	public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(Customer::class);
	}

	public function address(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(Address::class);
	}

	public function associateReserved($order, $customer, $properties)
	{
		$oldOrder = $customer->orders()
			->where('address_id', $properties->address->id)
			->where('status', static::STATUS_RESERVED)
			->isReserved()
			->latest()->first();
		if ($oldOrder) {
			$order->reserved()->associate($oldOrder);
		}

		return $order;
	}

	//Wallet

	public function statusLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(OrderStatusLog::class);
	}

	public function addItemsInOrder($order, $cart)
	{
		/**
		 * @var Variety $varietyItem 
		 */
		$varietyItem = $cart->variety;
		$gifts = $varietyItem->final_gift;
		$activeFlash = $varietyItem->product->activeFlash->first();
		/** @var OrderItem $item */
		$item = $order->items()->create([
			'product_id' => $varietyItem->product_id,
			'variety_id' => $varietyItem->id,
			'quantity' => $cart->quantity,
			'amount' => $cart->price,
			'flash_id' => $activeFlash->id ?? null,
			'discount_amount' => $varietyItem->final_price['discount_price'],
			'extra' => collect([
				'attributes' => $varietyItem->attributes()->get(['name', 'label', 'value']),
				'color' => $varietyItem->color()->exists() ? $varietyItem->color->name : null
			])->toJson()
		]);
		$item->gifts()->attach($gifts, ['gift' => $gifts?->toJson()]);
	}

	public static function getOrderStatisticsForCustomer(Customer $customer)
	{
		$statistics = [];
		foreach (static::getAvailableStatuses() as $status) {
			$statistics[$status] = $customer->orders()->where('status', $status)->count();
		}

		return $statistics;
	}

	public static function getAvailableStatuses(): array
	{
		return [
			static::STATUS_WAIT_FOR_PAYMENT,
			static::STATUS_IN_PROGRESS,
			static::STATUS_DELIVERED,
			static::STATUS_NEW,
			static::STATUS_CANCELED,
			static::STATUS_FAILED,
			static::STATUS_RESERVED
		];
	}

	public function getActivePaymentsAttribute()
	{
		$payments = [];
		$activePayment = $this->getActivePaymentAttribute();
		if ($activePayment) {
			$payments[] = $activePayment;
			foreach ($this->reservations as $reservation) {
				$activePayment = $reservation->getActivePaymentAttribute();
				if ($activePayment) {
					$payments[] = $activePayment;
				}
			}
		}

		return $payments;
	}

	// فاکتور هایی که یا کلا با کیف پول پرداخت شدن یا بخشیشون از کیف پول پرداخت شده
	public function getWalletInvoicesAttribute()
	{
		$final = [...$this->getSuccessWalletInvoices()];
		foreach ($this->reservations as $reservation) {
			$walletInvoices = $reservation->getSuccessWalletInvoices();
			if (count($walletInvoices)) {
				$final = [...$final, ...$walletInvoices];
			}
		}

		// Fake invoice from order logs (زمانی که فاکتور توسط ادمین ویرایش شده باشد)
		$orderLogs = $this->orderLogs;
		$changeAmount = 0;
		foreach ($orderLogs as $orderLog) {
			$changeAmount += $orderLog->amount;
		}
		if ($changeAmount !== 0) {
			$invoice = new Invoice();
			$invoice->forceFill([
				'amount' => $changeAmount,
				'status' => Invoice::STATUS_SUCCESS,
				'type' => Invoice::PAY_TYPE_WALLET
			]);
			$final = [...$final, $invoice];
		}

		return $final;
	}


	public function getStatusAttribute($status)
	{
		/**
		 * مهم است، پاک نشود در جاب استفاده میشود
		 */
		if ($status == static::STATUS_RESERVED && $this->reserved_id == null && $this->isReservedExpired()) {
			static::withoutEvents(function () {
				$this->update(['status' => static::STATUS_NEW]);
			});
			OrderStatusLog::store($this, $status);
		}

		return $status;
	}

	public function getActivitylogOptions(): LogOptions
	{
		$user = auth()->user();

		return LogOptions::defaults()
			->useLogName('Order')
			->logAll()
			->logOnlyDirty()
			->setDescriptionForEvent(function ($eventName) use ($user) {
				$eventName = Helpers::setEventNameForLog($eventName);
				return "سفارش با شناسه {$this->id} توسط '' {$eventName} شد.";
			});
	}

	#end of wallet

	public function setPayDescription($value)
	{
		$this->payDescription = $value;
	}

	//Scopes

	public function orderLogs()
	{
		return $this->hasMany(OrderLog::class, 'order_id')->latest('id');
	}

	//Custom functions

	public function canBuy(CustomerWallet $customer, int $quantity = 1, bool $force = null): bool
	{
		/**
		 * If the service can be purchased once, then
		 * @see OrderValidationService::checkAvailableVariety
		 */

		return !$customer->paid($this);
	}

	//Relations

	public function getAmountProduct(CustomerWallet $customer): int
	{
		return $this->total_amount;
	}

	public function getMetaProduct(): ?array
	{
		return [
			'customer_name' => $this->customer->full_name,
			'customer_mobile' => $this->customer->mobile,
			'description' => $this->payDescription ?: 'خرید سفارش به شماره #' . $this->id
		];
	}

	public function getUniqueId(): string
	{
		return (string)$this->getKey();
	}

	public function scopeMyOrders($query)
	{
		return $query->where('customer_id', '=', auth()->user()->id);
	}

	public function childs()
	{
		return $this->hasMany(Order::class, 'parent_id');
	}

	public function shipping(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(Shipping::class);
	}

	public function coupon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(Coupon::class);
	}

	public function reserved(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(static::class, 'reserved_id');
	}

	public function scopeParents($query)
	{
		$query->whereNull('reserved_id');
	}

	public function isPayable()
	{
		return $this->status === static::STATUS_WAIT_FOR_PAYMENT;
	}

	public function getPayableAmount()
	{
		return $this->total_amount;
	}
	public function scopeArrayIdsByDate($query, $startDay, $endDay): array
	{
		return static::query()
			->whereBetween('created_at', [$startDay, $endDay])
			->get('id')->pluck('id')->toArray();
	}

	public function scopeIsReserved($query)
	{
		$reservesDay = Setting::getFromName('reserved_day') ?: 0;
		$reservesDayStart = Carbon::now()->subDays($reservesDay)->toDateTimeString();
		$query->where('reserved', true)
			->whereNull('reserved_id')
			->whereBetween('created_at', [$reservesDayStart, Carbon::now()->toDateTimeString()]);
	}

	public function scopeIsActiveReserved($query)
	{

		$reservesDay = Setting::getFromName('reserved_day') ?: 0;
		$reservesDayStart = Carbon::now()->subDays($reservesDay)->toDateTimeString();
		$query->where('reserved', true)
			->where('status', static::STATUS_RESERVED)
			->whereNull('reserved_id')
			->whereBetween('created_at', [$reservesDayStart, Carbon::now()->toDateTimeString()]);
	}

	public function recalculateShippingAmount()
	{
		$this->shipping_amount = $this->calculateShippingAmount();
		$this->save();
		return $this->shipping_amount;
	}

	// Parent

	public function calculateShippingAmount()
	{
		$totalTotalQuantity = $this->getTotalTotalQuantity();
		return static::getPacketHelper(
			$totalTotalQuantity,
			$this->shipping->packet_size,
			$this->shipping_packet_amount,
			$this->shipping_more_packet_price,
			$this->shipping_first_packet_size
		);
	}


	public function getTotalTotalQuantity()
	{
		return $this->activeItems()->sum('quantity') + $this->activeReserved()
			->withSum('activeItems', 'quantity')
			->get()->sum('active_items_sum_quantity');
	}

	public function activeItems(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->items()->where('status', '=', 1);
	}

	public function isReservedExpired()
	{
		$reservesDay = Setting::getFromName('reserved_day') ?: 0;
		$reservesDayStart = Carbon::now()->subDays($reservesDay);

		return $this->created_at < $reservesDayStart;
	}

	public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(OrderItem::class, 'order_id')
			->with(['variety' => function ($query) {
				$query->withCommonRelations();
			}]);
	}



	public function activeReserved()
	{
		$reservesDay = Setting::getFromName('reserved_day') ?: 0;
		$reservesDayStart = Carbon::now()->subDays($reservesDay)->toDateTimeString();
		return $this->reservations()
			->where('status', static::STATUS_RESERVED)
			->whereNotNull('reserved_id')
			->whereBetween('created_at', [$reservesDayStart, Carbon::now()->toDateTimeString()]);
	}

	public function reservations(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(static::class, 'reserved_id', 'id')->withCommonRelations();
	}

	public function onFailedPayment(Invoice $invoice): View|Factory|JsonResponse|Application
	{
		$this->status = static::STATUS_FAILED;
		$this->status_detail = $invoice->status_detail;
		$this->save();

		NewOrderForCustomerNotificationJob::dispatch($this);

		return $this->callBackViewPayment($invoice);
	}

	public static function getPacketHelper($quantity, $packetSize, $price, $morePrice, $firstPacketSize, $oldQuantity = 0, $oldShippingAmountPaid = 0)
	{
		$allQuantity = $quantity + $oldQuantity;
		if ($allQuantity <= $firstPacketSize) {
			return $price - $oldShippingAmountPaid;
		}

		$totalPackets = (int)ceil((($allQuantity) - $firstPacketSize) / $packetSize);

		return (int)($price + $totalPackets * $morePrice) - $oldShippingAmountPaid;
	}

	public static function getAllStatuses($query)
	{
		$orderStatuses = [];
		foreach (static::getAvailableStatuses() as $status) {
			$orderStatuses[$status] = (clone $query)->where('status', $status)->count();
		}
		return $orderStatuses;
	}

	public function callBackViewPayment($invoice)
	{
		return (\Illuminate\Support\Facades\View::exists('basecore::invoice.callback')) ?
			view('basecore::invoice.callback', ['invoice' => $invoice, 'type' => 'order'])
			:
			view('core::invoice.callback', ['invoice' => $invoice, 'type' => 'order']);
	}

	public function scopeSuccess($query)
	{
		$query->whereIn('status', static::ACTIVE_STATUSES);
	}

	public function gift_package(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(GiftPackage::class, 'gift_package_id');
	}

	// محاسبه قیمت نهایی
	public function getTotalAmountAttribute(): int
	{
		$activeItems = $this->items->where('status', 1);
		$totalItemsAmount = $activeItems
			->reduce(function ($total, $item) {
				return $total + ($item->amount * $item->quantity);
			});
		$giftPackageAmount = isset($this->attributes['gift_package_price']) ? $this->attributes['gift_package_price'] : 0;

		return ($totalItemsAmount + $this->attributes['shipping_amount']) + $giftPackageAmount - $this->attributes['discount_amount'];
	}

	public function getTotalAmountForAdmin()
	{
		$activeItems = $this->items->where('status', 1);
		$totalItemsAmount = $activeItems
			->reduce(function ($total, $item) {
				return $total + ($item->amount * $item->quantity);
			});
		$giftPackageAmount = isset($this->attributes['gift_package_price']) ? $this->attributes['gift_package_price'] : 0;

		return ($totalItemsAmount + $this->attributes['shipping_amount']) + $giftPackageAmount;
	}

	public static function store(Customer $customer, $request)
	{
		/** @var Customer $user */
		$user = auth()->user();
		try {
			DB::beginTransaction();
			$ORDER = new static;
			/**
			 * @var Cart $fakeCart
			 * @var OrderStoreProperties $properties 
			 */
			$properties = $request->orderStoreProperties;
			$order = new static();

			$order->fill([
				'shipping_id' => $properties->shipping->id,
				'shipping_more_packet_price' => $properties->shipping->more_packet_price,
				'shipping_first_packet_size' => $properties->shipping->first_packet_size,
				'shipping_packet_amount' => $properties->shipping_packet_amount,
				'address' => $properties->address->toJson(),
				'coupon_id' => $properties->coupon ? $properties->coupon->id : null,
				'shipping_amount' => $properties->shipping_amount,
				'discount_amount' => $properties->discount_amount,
				'delivered_at' => $request->delivered_at,
				'status' => static::STATUS_WAIT_FOR_PAYMENT,
				'reserved' => $request->reserved ?? 0,
				'description' => $request->description,
				'gift_package_id' => $request?->gift_package_id,
				'gift_package_price' => $request->reserved ? 0 : $request->gift_package_price
			]);
			$order->customer()->associate($customer);
			$order->address()->associate($properties->address);
			if ($request->reserved) {
				$order = $ORDER->associateReserved($order, $customer, $properties);
			}
			$order->save();


			//Create status log
			$order->statusLogs()->create([
				'status' => static::STATUS_WAIT_FOR_PAYMENT
			]);

			//store items
			/**
			 * زمانی که خود مشتری میخره باید از کارت بره توی اوردر ولی
			 * زمانی که ادمین میخره کارت باید نادیده گرفته بشه
			 */
			if (auth()->user() instanceof Admin) {
				$fakeCart = new Cart();
				foreach ($request->varieties as $variety) {
					$baseVariety = Variety::query()->with(['product', 'product.activeFlash'])->findOrFail($variety['id']);
					$fakeCart->fill([
						'quantity' => $variety['quantity'],
					]);
					$fakeCart->setPrice($baseVariety);
					$fakeCart->variety()->associate($baseVariety);
					$ORDER->addItemsInOrder($order, $fakeCart);
				}
			} else {
				foreach ($properties->carts as $cart) {
					$ORDER->addItemsInOrder($order, $cart);
				} //End of foreach
			}
			/**
			 * کم کردن از انبار توی ایونت موقع پرداخت صورت میگیره
			 * @see  CheckStoreOnVerified::store Listener
			 * @see  GoingToVerifyPayment::__construct Event
			 */
			DB::commit();
		} catch (\Exception $exception) {
			DB::rollBack();
			throw $exception;
		}
		$order->load('items');

		return $order;
	}

	public function onSuccessPayment(Invoice $invoice)
	{
		$this->status = $this->reserved ? static::STATUS_RESERVED : static::STATUS_NEW;
		$this->save();
		$wallet = $invoice->type == 'wallet' ? 1 : 0;
		$type = $wallet ? 'از کیف پول' : 'از درگاه پرداخت';

		/** @var OrderItem $item */
		foreach ($this->items as $item) {
			if ($item->flash_id) {
				DB::table('flash_product')
					->where('product_id', $item->product_id)
					->where('flash_id', $item->flash_id)
					->update([
						'sales_count' => DB::raw("sales_count + {$item->quantity}")
					]);
			}
			Store::insertModel((object)[
				'variety_id' => $item->variety->id,
				'description' => "محصول {$item->variety->title} {$type} توسط مشتری با شناسه {$this->customer_id} در سفارش {$this->id} خریداری شد ",
				'type' => Store::TYPE_DECREMENT,
				'quantity' => $item->quantity,
				'order_id' => $this->id
			]);
		}

		//Send Sms For Admin
		$pattern = app(CoreSettings::class)->get('sms.patterns.success-order');

		$adminMobile = Setting::query()->where('name', 'mobile_per_success_order')
			->first();
		Sms::pattern($pattern)->data([
			'price' => number_format($invoice->amount * 10),
			'date' => verta(now())->formatDate(),
			'time' => verta(now())->formatTime(),
		])->to([$adminMobile->value])->send();


		// ذخیره سازی کوپن
		if ($this->coupon_id) {
			Coupon::useCoupon($this->customer_id, $this->coupon_id);
		}
		if ($this->reserved_id) {
			static::query()->findOrFail($this->reserved_id)
				->increment('shipping_amount', $this->shipping_amount);
			$this->shipping_amount = 0;
			$this->save();
		}
		/** Clear customer basket for new purchases  */
		$this->customer->carts()->delete();


		// notify with sms and notification for customer & notify new order to admin with email
		try {
			NewOrderForCustomerNotificationJob::dispatch($this);
			$this->customer->notify(new InvoicePaid($this));
			$adminEmail = Setting::getFromName('new_order_email_address');
			Mail::to($adminEmail)->send(new NewOrderEmail($invoice->payable));
		} catch (\Exception $e) {
			Log::error('error on notify new order to admin ', [$e->getMessage()]);
			Log::error('error on notify new order to admin ', [$e->getTraceAsString()]);
		}
		if ($wallet) {
			$data = [
				'order_id' => $invoice->payable_id,
				'invoice_id' => $invoice->id,
				'need_pay' => 0
			];

			return response()->success('خرید با موفقیت انجام شد.', $data);
		}

		return $this->callBackViewPayment($invoice);
	}

	public function scopeApplyFilter($query)
	{
		$id = request('id');
		$trackingCode = request('tracking_code');
		$city = request('city');
		$province = request('province');
		$firstName = request('first_name');
		$lastName = request('last_name');
		$customerId = request('customer_id');
		$status = request('status');
		$productId = request('product_id');
		$varietyId = request('variety_id');
		$startDate = request('start_date');
		$endDate = request('end_date');

		return $query->when($productId || $varietyId, function ($query) use ($productId, $varietyId) {
			$query->whereHas('items', function ($query) use ($productId, $varietyId) {
				$query
					->when($productId && !$varietyId, fn($q) => $q->where('product_id', $productId))
					->when($varietyId, fn($q) => $q->where('variety_id', $varietyId));
			});
		})
			->when($trackingCode, function ($query) use ($trackingCode) {
				$invoiceIds = Payment::query()->where('tracking_code', 'LIKE', "%" . $trackingCode . "%")->pluck('invoice_id');
				$orderIds = Invoice::query()->whereIn('id', $invoiceIds)->where('payable_type', Order::class)->pluck('payable_id');
				$query->whereIn('id', $orderIds);
			})
			->when($city, fn($q) => $q->where('address->city->name', 'LIKE', '%' . $city . '%'))
			->when($customerId, fn($q) => $q->where('customer_id', $customerId))
			->when($firstName, fn($q) => $q->where('address->first_name', 'LIKE', '%' . $firstName . '%'))
			->when($lastName, fn($q) => $q->where('address->last_name', 'LIKE', '%' . $lastName . '%'))
			->when($province, fn($q) => $q->where('address->city->province->name', 'LIKE', '%' . $province . '%'))
			->when($id, fn($q) => $q->where('id', $id))
			->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
			->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
			->when(isset($status), fn($q) => $q->where('status', $status));
	}
}

<?php

namespace Modules\Order\Jobs;

use Illuminate\Support\Str;
use Modules\Order\Entities\Order;
use Shetabit\Shopit\Modules\Sms\Sms;
use Modules\Core\Classes\CoreSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Kutia\Larafirebase\Messages\FirebaseMessage;
use Modules\Setting\Entities\Setting;
use Shetabit\Shopit\Modules\Core\Helpers\Helpers;

class ChangeStatusNotificationJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public $newStatus;

	public function __construct(public $order)
	{
		$this->newStatus = __('core::statuses.' . $order->status);
	}

	public function handle()
	{
		$tokens = DB::table('personal_access_tokens')
			->where('tokenable_type', 'Modules\Customer\Entities\Customer')
			->whereNotNull('device_token')
			->where('tokenable_id', $this->order->customer_id)
			->get('device_token')->pluck('device_token')->toArray();

		$via = explode(',', Setting::getFromName($this->order->status . '_type_sms'));
		if (in_array('firebase', $via)) {
			$this->firebase($this->order, $tokens);
		}
		if (in_array('sms', $via)) {
			$this->sms($this->order, $this->order->customer);
		}

		if (in_array('database', $via)) {
			$this->database($this->order, $this->order->customer_id);
		}
	}

	public function firebase($order, $tokens)
	{
		if (empty($tokens)) {
			return;
		}
		$orderId =  $this->order->reserved_id ?: $this->order->id;
		$message =  (new FirebaseMessage())
			->withTitle('وضعیت سفارش تغییر کرد')
			->withBody("مشتری عزیز وضعیت سفارش شما به {$this->newStatus} تغییر کرد")
			->withClickAction('order/' . $orderId);
		$message->asNotification(array_values(array_unique($tokens)));
	}

	public function database($order, $customerId)
	{
		$orderId =  $this->order->reserved_id ?: $this->order->id;
		DatabaseNotification::query()->create([
			'id' => Str::uuid(),
			'type' => 'order',
			'notifiable_type' => 'Modules\Customer\Entities\Customer',
			'notifiable_id' =>  $customerId,
			'data' => [
				'order_id' => $orderId,
				'description' => "مشتری عزیز وضعیت سفارش شما به {$this->newStatus} تغییر یافت"
			],
			'read_at' =>  null,
			'created_at' =>  now(),
			'updated_at' =>  now(),
		]);
	}

	public function sms($order, $customer)
	{
		$coreSettings = app(CoreSettings::class);
		$orderId =  $this->order->reserved_id ?: $this->order->id;
		$pattern = $coreSettings->get('sms.patterns.change_status');
		$address = json_decode($order->address);
		$full_name = $customer->full_name ?: $address->first_name . ' ' . $address->last_name;

		// ترتیب این کلید ها به هیچ وجه نباید عوض بشه
		$data = [
			// 'full_name' => str_replace(' ','_',$full_name),
			'status' => ($order->status == 'delivered') ? 'درحالـارسال' : str_replace(' ', '_', __('core::statuses.' . $order->status)),
			'order_id' => $orderId,
		];

		// اگر اس ام اس اختصاصی نفرستادی دیفالت و بفرست
		if (
			!$this->sendCustomSms($order, $customer, $data)
			&& in_array($order->status, [Order::STATUS_CANCELED, Order::STATUS_DELIVERED])
		) {
			Sms::pattern($pattern)->data($data)->to([$customer->mobile])->send();
		}
	}

	public function sendCustomSms($order, $customer, $data)
	{
		// چک کنیم آیا اس ام اس اختصاصی برای این وضعیت وجود دارد
		$coreSettings = app(CoreSettings::class);
		$customPattern = $coreSettings->get('sms.custom_patterns.order_' . $order->status);

		if ($customPattern && isset($customPattern['name'])) {
			if (isset($customPattern['keys'])) {
				// فقط کلید هایی که مشخص شده رو میفرستیم
				$newData = Helpers::getArrayIndexes($data, $customPattern['keys']);
			} else {
				$newData = $data;
			}
			Sms::pattern($customPattern['name'])->data($newData)->to([$customer->mobile])->send();

			return true;
		}

		return false;
	}
}

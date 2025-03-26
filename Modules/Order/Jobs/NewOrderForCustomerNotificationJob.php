<?php

namespace Modules\Order\Jobs;

use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;
use Kutia\Larafirebase\Messages\FirebaseMessage;
use Modules\Order\Entities\Order;
use Modules\Setting\Entities\Setting;
use Shetabit\Shopit\Modules\Core\Classes\CoreSettings;
use Shetabit\Shopit\Modules\Sms\Sms;
use Illuminate\Database\Eloquent\Collection;

class NewOrderForCustomerNotificationJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public array|Collection $admins;
  public $orderId;

  public function __construct(public $order) {}

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $this->orderId = $this->order->reserved_id ?: $this->order->id;
    $tokens = DB::table('personal_access_tokens')
      ->where('tokenable_type', 'Modules\Admin\Entities\Admin')
      ->whereNotNull('device_token')
      ->where('tokenable_id', $this->order->customer_id)
      ->get('device_token')->pluck('device_token')->toArray();

    $via = explode(',', Setting::getFromName('new_order_type_sms'));

    if (in_array('firebase', $via)) {
      $this->firebase($this->order, $tokens);
    }
    if (in_array('sms', $via)) {
      $this->sms($this->order);
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
    $body = $this->message();

    $message = (new FirebaseMessage())
      ->withTitle('سفارش جدید')
      ->withBody($body)
      ->withClickAction('order/' . $this->orderId);
    $message->asNotification(array_values(array_unique($tokens)));
  }

  public function message(): string
  {
    if ($this->order->status == Order::STATUS_FAILED) {
      return " مشتری عزیز، سفارش جدید به شناسه {$this->orderId} با خطا مواجه شد";
    }

    return "مشتری عزیز، سفارش جدید به شناسه {$this->orderId} ثبت برای شما شد";
  }

  public function database($order, $customerId)
  {
    $body = $this->message();
    DatabaseNotification::query()->create([
      'id' => Str::uuid(),
      'type' => 'order',
      'notifiable_type' => 'Modules\Customer\Entities\Customer',
      'notifiable_id' => $customerId,
      'data' => [
        'order_id' => $this->orderId,
        'description' => $body
      ],
      'read_at' => null,
      'created_at' => now(),
      'updated_at' => now(),
    ]);
  }

  //زمانی فاکتور پرداخت میشود ایمیل میره براش
  //    public function mail($customer){}

  public function sms($order)
  {
    $orderId = $this->order->reserved_id ?: $this->order->id;
    $customer = $this->order->customer;
    $address = json_decode($order->address);
    $full_name = empty($customer->full_name) 
      ? $address->first_name .' '. $address->last_name 
      : $customer->full_name;

    $coreSetting = app(CoreSettings::class);
    $pattern = $coreSetting->get('sms.patterns.new_order');
    $data = [
      'order_id' => $orderId,
    ];
    if (!$coreSetting->get('sms.new_order.dont_send_full_name')) {
      $data['full_name'] = $full_name;
    }

    if (!$coreSetting->get('sms.new_order.dont_send_status')) {
      $data['status'] = __('core::statuses.' . $order->status);
    }

    Sms::pattern($pattern)->data($data)->to([$customer->mobile])->send();
  }
}

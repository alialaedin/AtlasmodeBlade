<?php

namespace Modules\Order\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Order\Entities\Order;

class ChangeStatusToFailedJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected mixed $orders;
  protected mixed $gatewayTimeout;

  public function __construct()
  {
    $this->orders = Order::query()->where('status', Order::STATUS_WAIT_FOR_PAYMENT)->get();
    $this->gatewayTimeout = config('order.gateway_timeout') ?? 15;
  }

  public function handle()
  {
    foreach ($this->orders as $order) {
      $createdAt = Carbon::parse($order->created_at)->addMinutes($this->gatewayTimeout);
      if ($createdAt < now()) {
        $order->status = Order::STATUS_FAILED;
        $order->status_detail = 'خطا بدلیل عدم پرداخت توسط کاربر';
        $order->save();
      }
    }
  }
}

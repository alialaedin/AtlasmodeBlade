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
use Modules\Admin\Entities\Admin;
use Illuminate\Database\Eloquent\Collection;

class NewOrderForAdminNotificationJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public array|Collection $admins;
  protected $orderId;

  public function __construct(public $order)
  {
    $this->orderId = $this->order->reserved_id ?: $this->order->id;
    $this->admins = Admin::query()->select(['id', 'email'])->get()->each(function (Admin $item) {
      $role = $item->roles()->first();
      if ($item->hasRole('super-admin') || $role->hasAnyPermission(['read_order'])) {
        return $item;
      }
    });
  }

  public function handle(): void
  {
    $tokens = DB::table('personal_access_tokens')
      ->where('tokenable_type', 'Modules\Admin\Entities\Admin')
      ->whereNotNull('device_token')
      ->whereIn('tokenable_id', $this->admins->pluck('id')->toArray())
      ->get('device_token')->pluck('device_token')->toArray();

    $this->firebase($this->order, $tokens);
    $this->database($this->order, $this->admins);
  }

  public function firebase($order, $tokens)
  {
    if (empty($tokens)) {
      return;
    }

    $message =  (new FirebaseMessage())
      ->withTitle('سفارش جدید')
      ->withBody("سلام ادمین، سفارش جدید به شناسه {$order->id} ثبت شد")
      ->withClickAction('order/' . $this->orderId);
    $message->asNotification(array_values(array_unique($tokens)));
  }

  public function database($order, $admins)
  {
    $ids = $admins->pluck('id')->toArray();
    foreach ($ids as $id) {
      DatabaseNotification::query()->create([
        'id' => Str::uuid(),
        'type' => 'order',
        'notifiable_type' => 'Modules\Admin\Entities\Admin',
        'notifiable_id' =>  $id,
        'data' => [
          'order_id' => $this->orderId,
          'description' => "سلام ادمین، سفارش جدید با شناسه {$this->orderId} ثبت شد"
        ],
        'read_at' =>  null,
        'created_at' =>  now(),
        'updated_at' =>  now(),
      ]);
    }
  }
}

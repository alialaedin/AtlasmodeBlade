<?php

namespace Modules\Core\Channels;

use Illuminate\Notifications\Notification;

class SmsChannel
{
  /**
   * Send the given notification.
   *
   * @param  mixed  $notifiable
   * @param Notification $notification
   * @return void
   */
  public function send($notifiable, Notification $notification)
  {
    $smsInfo = $notification->toSms($notifiable);
  }
}

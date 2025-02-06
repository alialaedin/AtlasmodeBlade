<?php

namespace Modules\Core\Channels;

use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class FirebaseChannel
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
    $notification->toFirebase($notifiable);
  }
}

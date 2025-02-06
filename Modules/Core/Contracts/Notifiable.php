<?php

namespace Modules\Core\Contracts;

interface Notifiable
{
  public function notifications();

  public function notify($instance);
}

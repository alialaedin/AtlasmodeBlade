<?php

use Modules\Order\Entities\Order;

return [
    'name' => 'Admin',

    'orderStatusColorsForDashboard' => [
        Order::STATUS_IN_PROGRESS => 'warning',
        Order::STATUS_NEW => 'info',
        Order::STATUS_DELIVERED => 'success'
    ]
];

<?php

use Modules\Order\Entities\Order;

return [
    'name' => 'Order',
    'gateway_timeout' => 15,

    'statusLabels' => [
        Order::STATUS_CANCELED => 'کنسل شده',
        Order::STATUS_DELIVERED => 'ارسال شده',
        Order::STATUS_WAIT_FOR_PAYMENT => 'در انتطار پرداخت',
        Order::STATUS_NEW => 'جدید',
        Order::STATUS_RESERVED => 'رزرو شده',
        Order::STATUS_FAILED => 'خطا',
        Order::STATUS_IN_PROGRESS => 'در انتطار تکمیل',
    ],

    'statusColors' => [
        Order::STATUS_WAIT_FOR_PAYMENT => 'btn-rss',
        Order::STATUS_NEW => 'btn-primary',
        Order::STATUS_IN_PROGRESS => 'btn-secondary',
        Order::STATUS_DELIVERED => 'btn-success',
        Order::STATUS_CANCELED => 'btn-pinterest',
        Order::STATUS_FAILED => 'btn-youtube',
        Order::STATUS_RESERVED => 'btn-info',
    ],

    'orderPaginations' => [15, 30, 50, 100]

];

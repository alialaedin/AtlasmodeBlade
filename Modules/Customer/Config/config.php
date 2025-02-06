<?php

use Modules\Customer\Entities\Withdraw;

return [
    'name' => 'Customer',
    'withdraw_statuses' => [
        Withdraw::STATUS_PAID => 'پرداخت شده',
        Withdraw::STATUS_PENDING => 'در انتظار تکمیل',
        Withdraw::STATUS_CANCELED => 'لغو شده',
    ],
];

<?php

use Modules\Slider\Entities\Slider;

return [
    'name' => 'Slider',
    'groups' => [
        'header',
        'header-mobile'
    ],
    'groupLabels' => [
        Slider::GROUP_DESKTOP => 'دسکتاپ',
        Slider::GROUP_MOBILE => 'موبایل',
    ]
];

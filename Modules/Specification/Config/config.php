<?php

use Modules\Specification\Entities\Specification;

return [

	'name' => 'Specification',

	'types' => [
		Specification::TYPE_TEXT => 'متنی',
		Specification::TYPE_SELECT => 'تک مقدار',
		Specification::TYPE_MULTI_SELECT => 'چند مقدار'
    ],

    'select_types' => [
        Specification::TYPE_SELECT,
        Specification::TYPE_MULTI_SELECT
    ]

];
<?php

use Modules\Attribute\Entities\Attribute;

return [
  'name' => 'Attribute',
  'translates' => [
    'types' => [
      Attribute::TYPE_TEXT => 'متنی',
      Attribute::TYPE_SELECT => 'انتخابی',
    ],
    'styles' => [
      Attribute::STYLE_BOX => 'مربعی',
      Attribute::STYLE_SELECT => 'کومبو باکس'
    ]
  ]
];

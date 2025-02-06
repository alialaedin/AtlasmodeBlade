<?php

use Modules\Product\Entities\SpecificDiscountItem;
use Modules\Product\Entities\Product;

return [
    'name' => 'Product',

    "messages" => [
        'product.discount.required_with' => "لطفا نوع تخفیف محصول را مشخص کنید",
        'product.quantity.required_without' => "درصورت نبود تنوع وارد کردن تعداد محصول اجباری است",
        'product.title.required' => 'عنوان محصول الزامی است',
        'product.status.required' => 'فیلد وضعیت الزامی است'
    ],

    'specificDiscountItemTypes' => [
        SpecificDiscountItem::TYPE_BALANCE => 'موجودی',
        SpecificDiscountItem::TYPE_CATEGORY => 'دسته بندی',
        SpecificDiscountItem::TYPE_PRODUCT => 'محصولات',
        SpecificDiscountItem::TYPE_RANGE => 'بازه قیمت'
    ],

    'prdocutStatusLabels' => [
        Product::STATUS_DRAFT => 'پیش نویس',
        Product::STATUS_SOON => 'به زودی',
        Product::STATUS_AVAILABLE => 'موجود',
        Product::STATUS_OUT_OF_STOCK => 'ناموجود',
        Product::STATUS_AVAILABLE_OFFLINE => 'موجود برای فروش حضوری',
        Product::STATUS_INIT_QUANTITY => 'موجودی اولیه',
    ],

    'prdocutStatusColors' => [
        Product::STATUS_DRAFT => 'primary',
        Product::STATUS_SOON => 'secondary',
        Product::STATUS_AVAILABLE => 'success',
        Product::STATUS_OUT_OF_STOCK => 'danger',
        Product::STATUS_AVAILABLE_OFFLINE => 'success',
        Product::STATUS_INIT_QUANTITY => 'pink',
    ],

    'productDiscountTypes' => [
        Product::DISCOUNT_TYPE_FLAT => 'مقدار ثابت',
        Product::DISCOUNT_TYPE_PERCENTAGE => 'درصد',
    ]

];

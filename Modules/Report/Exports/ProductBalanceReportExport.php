<?php

namespace Modules\Report\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ProductBalanceReportExport implements FromCollection
{
    public function __construct(protected $array){}

    public function collection()
    {
        $finalModels = [];

        $finalModels[] = [
            'شناسه محصول',
            'عنوان محصول',
            'قیمت خرید',
            'قیمت فروش',
            'موجودی فعلی',
            'موجودی در تاریخ',
            'سرمایه بر اساس قیمت خرید',
            'سرمایه بر اساس قیمت فروش'
        ];

        foreach ($this->array as $item) {
            $finalModels[] = $item;
        }

        $finalModels = collect([...$finalModels]);

        return $finalModels;
    }
}

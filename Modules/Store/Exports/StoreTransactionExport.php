<?php

namespace Modules\Store\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class StoreTransactionExport implements FromCollection
{
    public function __construct(protected $array){
        $this->collection();
    }

    public function collection()
    {

        $finalModels = [];
        $finalModels[] = [
            'شناسه',
            'محصول',
            'عامل',
            'توضیح',
            'تعداد',
            'نوع تغییرات',
            'تاریخ ثبت',
        ];

        foreach ($this->array as $item) {


            $finalModels[] = $item;
        }

        $finalModels = collect([...$finalModels]);

        return $finalModels;
    }
}

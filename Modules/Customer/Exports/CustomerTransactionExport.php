<?php

namespace Modules\Customer\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;


class CustomerTransactionExport implements FromCollection
{

    public function __construct(protected $array){}

    public function collection()
    {
        $finalModels = [];

        $finalModels[] = [
            'شناسه',
            'نوع',
            'وضعیت',
            'توضیحات',
            'تاریخ',
        ];

        foreach ($this->array as $item) {
            $finalModels[] = $item;
        }

        $finalModels = collect([...$finalModels]);

        return $finalModels;
    }

}

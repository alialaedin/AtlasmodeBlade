<?php

namespace Modules\Report\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class SiteViewReportExport implements FromCollection
{
    public function collection()
    {
        $finalModels = [];
        $finalModels[] = [
            'تاریخ',
            'تعداد بازدید',
            'مشاهده جزئیات',
        ];
        foreach ($this->models as $model) {
            $item = [];
            $item[] = verta($model->date)->format('Y/m/d');
            $item[] = $model->total_count;

            $finalModels[] = $item;
        }
        $finalModels = collect([...$finalModels]);

        return $finalModels;
    }

    public function __construct(protected $models){}

}

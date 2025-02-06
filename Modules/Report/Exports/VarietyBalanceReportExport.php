<?php

namespace Modules\Report\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class VarietyBalanceReportExport implements FromCollection
{
    public function __construct(protected $models){}

    public function collection()
    {
        $finalModels = [];
        $finalModels[] = [
            'شناسه تنوع	',
            'تنوع',
            'موجودی انبار',
        ];
        foreach ($this->models as $model) {
            $item = [];
            $m = $model->toArray();
            $item[] = $m['id'];
            $item[] = $m['title_showcase']['fullTitle'];
            $item[] = $m['store_balance'];
            $finalModels[] = $item;
        }
        $finalModels = collect([...$finalModels]);

        return $finalModels;
    }
}

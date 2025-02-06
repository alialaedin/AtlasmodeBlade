<?php

namespace Modules\Report\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class OrderReportExport implements FromCollection
{
    public function collection()
    {
        $finalModels = [];
        $finalModels[] = [
            'شناسه سفارش',
            'تعداد اقلام',
            'تخفیف کوپن',
            'تخفیف آیتم ها',
            'تخفیف روی سفارش',
            'هزینه حمل و نقل',
            'مبلغ پرداخت شده',
        ];
        foreach ($this->models as $model) {
            $item = [];
//            $m = $model->toArray();
            $item[] = $model->id;
            $item[] = $model->total_items_count;
            $item[] = $model->discount_on_coupon;
            $item[] = $model->discount_on_order;
            $item[] = $model->discount_on_items;
            $item[] = $model->shipping_amount;
            $item[] = $model->total_invoices_amount;

            $finalModels[] = $item;
        }
        $finalModels = collect([...$finalModels]);

        return $finalModels;
    }

    public function __construct(protected $models){}

}

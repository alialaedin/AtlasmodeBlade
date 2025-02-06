<?php

namespace Modules\Store\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Product\Entities\Product;

class StoreBalanceReport implements FromCollection
{
    public function __construct(protected $products){}

    public function collection()
    {
        $finalModels = [];

        /*$finalModels[] = [
            'ردیف',
            'عنوان محصول',
            'موجودی محصول',
            'آستانه موجودی',
            'تاریخ آستانه',
        ];*/



        foreach ($this->products as $product) {
            $finalModels[] = [
                'شناسه محصول',
                'عنوان محصول',
                'تعداد کل موجودی محصول',
                'وضعیت محصول',
            ];
            $finalModels[] = [
                $product->id,
                $product->title,
                $product->total_balance,
                $this->translateStatus($product->status),
            ];

            $finalModels[] = [
                'عنوان تنوع',
                'بارکد',
                'SKU',
                'موجودی',
            ];

            foreach ($product->varieties as $variety) {
                $finalModels[] = [
                    $variety['title'],
                    $variety['barcode'],
                    $variety['SKU'],
                    $variety['balance'],
                ];
            }
        }

        $finalModels = collect([...$finalModels]);

        return $finalModels;
    }

    private function translateStatus($status):string
    {
        switch ($status) {
            case Product::STATUS_AVAILABLE:
                return 'فعال';
            case Product::STATUS_OUT_OF_STOCK:
                return 'ناموجود';
            case Product::STATUS_AVAILABLE_OFFLINE:
                return 'فعال در فروشگاه حضوری';
            case Product::STATUS_DRAFT:
                return 'پیش نویس';
            case Product::STATUS_SOON:
                return 'به زودی';
            default :
                return $status;
        }
    }
}

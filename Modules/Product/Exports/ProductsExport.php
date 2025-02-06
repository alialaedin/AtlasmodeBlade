<?php

namespace Modules\Product\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements FromCollection
{
    public function __construct(public $data) {}

    public function collection()
    {
        return collect($this->data);
    }
}

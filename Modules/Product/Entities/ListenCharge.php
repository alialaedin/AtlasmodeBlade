<?php

namespace Modules\Product\Entities;

use Illuminate\Http\Request;
use Modules\Customer\Entities\Customer;
use Shetabit\Shopit\Modules\Product\Entities\ListenCharge as BaseListenCharge;

class ListenCharge extends BaseListenCharge
{
    protected $fillable = [
        'customer_id','variety_id',
    ];


    public static function storeListenCharge(Customer $customer, $variety)
    {
        $variety = Variety::findOrFail($variety);

        if ($listenCharge = static::where('customer_id', $customer->id)
            ->where('variety_id', $variety->id)->first()
        ) {
            return $listenCharge;
        }

        $listenCharge = ListenCharge::create([
            'variety_id' => $variety->id,
            'customer_id' => $customer->id,
        ]);

        return $listenCharge;
    }


}

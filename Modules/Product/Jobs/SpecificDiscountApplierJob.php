<?php

namespace Modules\Product\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Product\Entities\SpecificDiscount;

class SpecificDiscountApplierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $specificDiscounts = SpecificDiscount::query()
            ->where('start_date', '<=', now())
            ->whereNull('done_at')
            ->orderBy('created_at')
            ->get();


        foreach ($specificDiscounts as $specificDiscount) {
            $types = $specificDiscount->types;
            foreach ($types as $type) {
                $items = $type->items;
                foreach ($items as $item) {
                    $item->apply_discounts();
                }
            }
            $specificDiscount->done_at = now();
            $specificDiscount->save();
        }




    }
}

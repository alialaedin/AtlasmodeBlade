<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Cart\Jobs\RemoveOldCartsJob;
use Modules\Order\Jobs\ChangeStatusToFailedJob;
use Modules\Product\Jobs\SpecificDiscountApplierJob;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Shetabit\Shopit\Modules\Order\Jobs\RemoveFailedOrdersJob;
use Shetabit\Shopit\Modules\Order\Jobs\ReservationTimeOutNotificationJob;
use Shetabit\Shopit\Modules\Product\Jobs\CheckDiscountUntilJob;

class Kernel extends ConsoleKernel {
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('shopit:sitemap')->everyThirtyMinutes();
        $schedule->job(ChangeStatusToFailedJob::class)->at('04:00:00');
        $schedule->job(ReservationTimeOutNotificationJob::class)->everyMinute();
        $schedule->job(CheckDiscountUntilJob::class)->everyMinute();
        $schedule->job(RemoveFailedOrdersJob::class)->everyMinute();
        $schedule->job(RemoveOldCartsJob::class)->hourly();
        $schedule->job(SpecificDiscountApplierJob::class)->everyTenMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        $this->load(module_path('Core').'/Commands');

        require base_path('routes/console.php');
    }

}


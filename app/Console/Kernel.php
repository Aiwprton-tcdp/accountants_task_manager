<?php

namespace App\Console;

use App\Traits\UserTrait;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(fn() => UserTrait::hotPaymentsNotifyPreparing())
            ->name('payments.hot.notify')
            // ->weekdays()
            ->everyMinute();
            // ->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

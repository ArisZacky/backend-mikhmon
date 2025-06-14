<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SendBillingToExternalApi::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('billing:post-to-api')
                 ->everySixHours()
                 ->appendOutputTo(storage_path('logs/billing-cron.log'));
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

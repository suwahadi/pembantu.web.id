<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Process payout queue daily
        $schedule->command('queue:work', ['--queue' => 'payouts', '--max-jobs' => 10])
            ->dailyAt('02:00')
            ->name('process-payouts')
            ->withoutOverlapping();
        
        // Process refund queue daily
        $schedule->command('queue:work', ['--queue' => 'refunds', '--max-jobs' => 10])
            ->dailyAt('02:30')
            ->name('process-refunds')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

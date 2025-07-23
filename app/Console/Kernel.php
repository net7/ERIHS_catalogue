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
        $schedule->command('app:send-data-to-cordra')
            ->withoutOverlapping()
            ->daily(); //Run the task every day at midnight
        $schedule->command('app:notify-reviewer')
            ->withoutOverlapping()
            ->daily(); //Run the task every day at midnight
        $schedule->command('app:reset-reviews')
            ->withoutOverlapping()
            ->yearly(); //Run the task on the first day of every year at 00:00
        $schedule->command('app:require-post-access-duties')
            ->withoutOverlapping()
            ->weekly(); //Run the task every Sunday at 00:00.
        $schedule->command('app:process-calls-closure')
            ->withoutOverlapping()
            ->daily();
        $schedule->command('app:check-files')
            ->withoutOverlapping()
            ->daily(); //Run the task every day at midnight
        $schedule->command('app:solicit-service-manager-to-close-access')
            ->withoutOverlapping()
            ->daily(); //Run the task every day at midnight
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

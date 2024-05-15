<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Install',
        'App\Console\Commands\SearchableRefresh',
        'App\Console\Commands\Permissions\UpdateCommand',
        'App\Console\Commands\Masungi\ThankYouNotification',
        'App\Console\Commands\Masungi\TrailRequestReminderNotification',
        'App\Console\Commands\Masungi\RemainingBalanceReminderNotification',
        'App\Console\Commands\Masungi\LapsedPaymentNotification',
        'App\Console\Commands\Masungi\ExpiredVisitRequestNotification',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /* Masungi Notifications */
        
        $schedule->command('send:trail_request_reminder')
            ->daily();

        $schedule->command('send:thank_you')
            ->daily();

        /* Unused since the email workflow adjustments from the client */
        $schedule->command('send:remaining_balance_reminder')
            ->daily();
            
        $schedule->command('send:lapsed_payment')
            ->dailyAt('00:00');

        $schedule->command('send:expired_visit_request')
            ->dailyAt('00:00');
        /* End of Masungi Notifications */
        
        Log::info('Schedule Running '. Carbon::now());
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

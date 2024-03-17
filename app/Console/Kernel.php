<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule as Scheduler;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Schedule;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Scheduler $scheduler): void
    {
        date_default_timezone_set('America/Sao_Paulo');
        $scheduler->call(function () {
            Log::info('Job executed', [date("Y-m-d H:i:s")]);
            
            $today = date("Y-m-d");
            $tomorrow = date("Y-m-d", strtotime($today . ' + 1 days'));
            $twoDays = date("Y-m-d", strtotime($today . ' + 2 days'));
            Log::info('Date: ', [$today, $tomorrow, $twoDays]);

            $schedules = Schedule::with("client", "scheduleItems")
                ->where("date", $tomorrow)
                ->where("confirmed", "0")
                ->where("canceled", "0")
                ->where("done", "0")
                ->get();
            foreach ($schedules as $schedule) {
                \App\Jobs\Sms::dispatch($schedule);
                \App\Jobs\EmailConfirmation::dispatch($schedule);

                Log::info('Sending confirmation', [$schedule->client]);
            }

            $schedulesTwoDays = Schedule::with("company", "client", "scheduleItems.service", "scheduleItems.employee")
                ->where("date", $twoDays)
                ->where("confirmed", "0")
                ->where("canceled", "0")
                ->where("done", "0")
                ->get();
            foreach ($schedulesTwoDays as $schedule) {
                \App\Jobs\EmailReminder::dispatch($schedule);

                Log::info('Sending reminder', [$schedule->client]);
            }
        })->timezone('America/Sao_Paulo')->dailyAt('09:50');

        // $schedule->command('backup:clean')->daily()->at('01:00');
        // $schedule->command('backup:run')->twiceDaily(2, 14);
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

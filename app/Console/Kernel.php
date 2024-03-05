<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        date_default_timezone_set('America/Sao_Paulo');
        $schedule->call(function () {
            Log::info('Job executado' . date("Y-m-d H:i:s"));

            $proximaData = date("Y-m-d", strtotime(date("Y-m-d") . ' + 1 days'));

            $schedules = Schedule::with('client')->where("date", $proximaData)->where("confirmed", "0")->get();
            foreach ($schedules as $schedule) {
                \App\Jobs\Sms::dispatch($schedule);
                \App\Jobs\Email::dispatch($schedule);

                Log::info('Enviando sms para ' . $schedule->client->name);
            }
        })->timezone('America/Sao_Paulo')->dailyAt('08:00');

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

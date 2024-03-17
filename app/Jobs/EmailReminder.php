<?php

namespace App\Jobs;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function handle(): void
    {
        Log::info('Sending reminder e-mail', [$this->schedule]);

        $this->schedule->date = date("d/m/Y", strtotime($this->schedule->date));
        Mail::send('mails.reminder', [
            'schedule' => $this->schedule,
        ], function ($message) {
            $message->to($this->schedule->client->email);
            $message->subject('Skedyou - Lembrete de agendamento');
            $message->from('suporte@skedyou.com', 'Equipe Skedyou');
        });
    }
}

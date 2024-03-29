<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailConfirmation implements ShouldQueue
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
        Log::info('Sending confirmation e-mail', [$this->schedule]);
        $confirmationUrl = env("SITE_URL") . "/confirm-schedule?hash=" . $this->schedule->confirmed_hash;

        $this->schedule->date = date("d/m/Y", strtotime($this->schedule->date));
        Mail::send('mails.confirmation', ['schedule' => $this->schedule, 'confirmationUrl' => $confirmationUrl], function ($message) {
            $message->to($this->schedule->client->email);
            $message->subject('Skedyou - Confirmação de agendamento');
            $message->from('suporte@skedyou.com', 'Equipe Skedyou');
        });
    }
}

<?php

namespace App\Jobs;

date_default_timezone_set('America/Sao_Paulo');

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailConfirmation implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $schedule;

    public function __construct($schedule) {
        $this->schedule = $schedule;
    }

    public function handle(): void {
        Log::info('Sending confirmation e-mail', [$this->schedule]);

        Mail::send('mails.confirmation', ['schedule' => $this->schedule], function ($message) {
            $message->to($this->schedule->client->email);
            $message->subject('Skedyou - Confirmação de agendamento');
            $message->from('naoresponda@skedyou.app', 'Equipe Skedyou');
        });
    }
}

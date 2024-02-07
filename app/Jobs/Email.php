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

class Sms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $schedule;
    public $smsController;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function handle(): void
    {
        Log::info('Enviando email para ' . $this->schedule->client->name);
        Mail::send('mails.confirmacao', ['schedule' => $this->schedule], function($message) {
            $message->to($this->schedule->client->email);
            $message->subject('Skedyou -Confirmação de agendamento!');
            $message->from('suporte@skedyou.com','Equipe Skedyou'); 
        });
    }
}

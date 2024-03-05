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

class Email implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $schedule;
    public $scheduleItems;

    public function __construct($schedule, $items)
    {
        $this->schedule = $schedule;
        $this->scheduleItems = $items;
    }

    public function handle(): void
    {
        Log::info('Enviando email para ' . $this->schedule->client->name);
        $confirmationUrl = env("SITE_URL") . "/confirm-schedule?hash=" . $this->schedule->confirmed_hash;
        Mail::send('mails.confirmation', ['schedule' => $this->schedule, 'scheduleItems' => $this->scheduleItems, 'confirmationUrl' => $confirmationUrl], function($message) {
            $message->to($this->schedule->client->email);
            $message->subject('Skedyou - Confirmação de agendamento!');
            $message->from('suporte@skedyou.com','Equipe Skedyou'); 
        });
    }
}

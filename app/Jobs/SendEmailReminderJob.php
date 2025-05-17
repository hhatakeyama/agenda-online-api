<?php

namespace App\Jobs;

date_default_timezone_set('America/Sao_Paulo');

use App\Models\LogDisparo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailReminderJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $schedule;

    public function __construct($schedule) {
        $this->schedule = $schedule;
    }

    public function handle(): void {
        Log::info('Sending reminder e-mail', [$this->schedule]);

        $endereco = $this->schedule->company->address . ', ' . $this->schedule->company->number . ', ' . $this->schedule->company->district . ', ' . $this->schedule->company->city->name . '/' . $this->schedule->company->state;
        $data = [
            'schedule' => $this->schedule,
            'endereco' => $endereco,
        ];
        Mail::send('mails.reminder', $data, function ($message) {
            $message->to($this->schedule->client->email);
            $message->subject('Skedyou - Lembrete de agendamento');
            $message->from('naoresponda@skedyou.app', 'Equipe Skedyou');
        });

        LogDisparo::create([
            'tipo' => 'email',
            'destinatario' => $this->schedule->client->email,
            'assunto' => json_encode("Skedyou - Lembrete de agendamento"),
            'mensagem' => json_encode(""),
            'status' => 'enviado',
            'data_envio' => now(),
        ]);
    }
}

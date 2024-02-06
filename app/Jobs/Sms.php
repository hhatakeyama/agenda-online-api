<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SMSController;

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
        $this->$smsController = new SMSController();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //remove possiveis caracteres especiais
        $telefone = preg_replace('/[^0-9]+/i', '', $this->schedule->client->mobilePhone);

        //o numero precisa ser de celular e o horario de agendamento precisa estar preenchido
        if (empty($this->schedule->client->mobilePhone)){
            Log::info("SMS não enviado, horário de agendamento não preenchido ou cliente sem celular", [$this->schedule]);
            return;
        }

        $message = 'Olá, ' . $this->schedule->client->name . ' seu agendamento para o dia ' . date("d/m/Y", strtotime($this->schedule->date)) . ' às ' . date("H:i", strtotime($this->schedule->start_time)) . ' foi confirmado? Responda 1 para Sim ou 2 para Não.';
        
        $smsController->sendMessage($this->schedule, $message);
    }
}

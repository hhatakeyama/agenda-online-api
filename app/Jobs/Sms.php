<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Sms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $schedule;
    public $mobilePhoneTest;

    public function __construct($schedule, $mobilePhoneTest)
    {
        $this->schedule = $schedule;
        $this->mobilePhoneTest = $mobilePhoneTest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Enviando SMS", [$this->schedule]);

        $mobilePhone = $this->mobilePhoneTest ? $this->mobilePhoneTest : $this->schedule->client->mobilePhone;
        $mobile = preg_replace('/[^0-9]+/i', '', $mobilePhone);

        //o numero precisa ser de celular e o horario de agendamento precisa estar preenchido
        if (empty($mobile)) {
            Log::info("SMS não enviado, cliente não preencheu o celular", [$this->schedule]);
            return;
        }

        $date = date("d/m/Y", strtotime($this->schedule->date));
        $message = 'Olá, ' . $this->schedule->client->name . ' seu agendamento para o dia ' . $date . ' às ' . $this->schedule->scheduleItems[0]->start_time . ' foi confirmado? Responda 1 para Sim ou 2 para Não.';

        $curlUrl = "https://api.smsdev.com.br/v1/send?key=" . env("TOKEN_SMS_DEV") . "&type=9&number=" . $mobile . "&refer=confirm|" . $this->schedule->confirmed_hash . "&msg=" . urlencode($message);
        Log::info("Teste envio SMS", [$message, $curlUrl]);
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $curlUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

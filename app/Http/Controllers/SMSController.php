<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SMSController extends Controller
{
    public function sendMessage($schedule, $message)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.smsdev.com.br/v1/send?key=" . getenv("TOKEN_SMS_DEV") . "&type=9&number=" . $schedule->client->mobilePhone . "&refer=" . $schedule->confirmed_hash . "&msg=".urlencode($message),
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
    }

    public function receiveResponseFromMessage()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.smsdev.com.br/v1/inbox?key=" . getenv("TOKEN_SMS_DEV") . "&status=1",
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
        foreach (json_decode($response) as $item) {
            $this->confirmationScheduleMessage($item);
        }
        $err = curl_error($curl);

        curl_close($curl);
    }
    
    public function confirmationScheduleMessage($response)
    {
        Log::info("Received schedule to confirmed", [$response]);
        $shedule = Schedule::where('confirmed_hash', $response->refer)->firstOrFail();
        $shedule->confirmed = $response->descricao == 1 ? true : false;
        if ($shedule->save()) {
            Log::info("Schedule Confimated", [$shedule]);
            return response()->json([
                "message" => "Agendamento confirmado"
            ], 200);
        } else {
            Log::error("Error confirm schedule", [$shedule]);
            return response()->json([
                "message" => "Erro ao confirmar agendamento"
            ], 400);
        }
    }
}

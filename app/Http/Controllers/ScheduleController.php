<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleItem;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Hash;

class ScheduleController extends Controller
{
    public function getSheduleFromEmployee(Request $request)
    {
        $schedules = Schedule::with("scheduleItems", "client")->where("company_id", $request->company_id)->where("date", "=", $request->date)->get();
        $schedulesFromEmployees = [];
        foreach ($schedules as $item) {
            foreach ($item->scheduleItems as $scheduleItem) {
                if (!array_key_exists($scheduleItem->employee_id, $schedulesFromEmployees)) {
                    $schedulesFromEmployees[$scheduleItem->employee_id] = [];
                }
                array_push($schedulesFromEmployees[$scheduleItem->employee_id], $scheduleItem);
            }
        }
        return response()->json([
            "data" => $schedulesFromEmployees
        ], 200);
    }

    public function getSheduleFromCliente($client_id)
    {
        $schedule = Schedule::with("scheduleItems.employee")->where("client_id", $client_id)->paginate(10);
        return response()->json([
            "message" => "Agenda do cliente"
        ], 200);
    }

    public function getSchedulesFromEmployeesBeginningToday()
    {
        Log::info("Searching schedules from employees beginning today");
        $schedule = Schedule::with("scheduleItems")->where("date", ">=", date("Y-m-d"))->get();
        return response()->json([
            "data" => $schedule
        ], 200);
    }

    public function getSchedulesFromEmployeeBeginningToday($employee_id)
    {
        Log::info("Searching schedules from employee beginning today");
        $schedule = Schedule::with("scheduleItems")->whereHas("scheduleItems", function ($query) use ($employee_id) {
            $query->where("employee_id", $employee_id);
        })->where("date", ">=", date("Y-m-d"))->get();
        return response()->json([
            "data" => $schedule
        ], 200);
    }

    public function create(Request $request)
    {
        Log::info("Creating schedule", [$request]);
        $validated = $request->validate([
            'client_id' => 'required',
            'company_id' => 'required',
            'date' => 'required',
            'items' => 'required',
        ]);

        $scheduleData = $request->all();
        $scheduleData['confirmed_hash'] = Hash::make($request->company_id + $request->employee_id + date('YmdHis'));
        $schedule = Schedule::create($scheduleData);
        if ($schedule->save()) {
            if ($request->items) {
                foreach ($request->items as $item) {
                    $this->createScheduleItems($schedule->id, $item);
                }
            }

            // Essa mensagem vai no template da view do email
            $sms_message = "Seu agendamento foi realizado para o dia " . date("d/m/Y", strtotime($schedule->date)) . " às " . date("H:i", strtotime($schedule->start_time));
            // new \App\Mail\Schedule($schedule);

            Log::info("Schedule created", [$schedule]);
            return response()->json([
                "message" => "Agendamento criado com sucesso",
            ], 200);
        } else {
            Log::error("Error create schedule", [$request]);
            return response()->json([
                "message" => "Erro ao criar agendamento. Verifique se os campos foram preenchidos corretamente ou tente novamente mais tarde.",
            ], 400);
        }
    }

    // public function smsAviso($sms = false, $email = false)
    // {
    //     Log::info('Cron job manual executado' . date("Y-m-d H:i:s"));

    //     date_default_timezone_set('America/Sao_Paulo');
    //     $hoje = date("Y-m-d");
    //     if ($hoje != "2018-06-12") {
    //         $leads = Lead::where("data_voucher", $hoje)->get();
    //         foreach ($leads as $lead) {
    //             $promocao = Promocao::find($lead->promocao_id);
    //             if ($sms !== false && $sms !== 'false') {
    //                 Log::info('Enviando SMS para: ' . $lead->telefone);
    //                 \App\Jobs\SmsAviso::dispatch($lead, $promocao);
    //                 Log::info('SMS enviado para: ' . $lead->telefone);
    //             }

    //             if ($email !== false) {
    //                 Log::info('Enviando e-mail para: ' . $lead->email);
    //                 $date = date('d/m/Y', strtotime($lead->data_voucher));
    //                 $lead->dia = $date;
    //                 Mail::to([$lead->email])
    //                     ->queue(new \App\Mail\Aviso($lead, $promocao, $lead->unidade, $date, $lead->periodo->nome));
    //                 Log::info('E-mail enviado para: ' . $lead->email);
    //             }
    //         }
    //     }
    // }

    private function createScheduleItems($schedule_id, $item)
    {
        Log::info("Create schedule item", [$item]);
        $item['schedule_id'] = $schedule_id;
        $schedule_item = ScheduleItem::create($item);
        if ($schedule_item->save()) {
            Log::info("Schedule Item created", [$schedule_item]);
        } else {
            Log::error("Error create schedule item", [$item]);
        }
    }

    public function update(Request $request)
    {
        $shedule = Schedule::find($request->id);
        $shedule->fill($request->all());
        if ($shedule->save()) {
            Log::info("Schedule updated", [$shedule]);
            return response()->json([
                "message" => "Agendamento atualizado"
            ], 200);
        } else {
            Log::error("Error update schedule", [$shedule]);
            return response()->json([
                "message" => "Erro ao atualizar agendamento"
            ], 400);
        }
    }

    private function updateScheduleItems($schedule_id, $schedule_item)
    {
        Log::info("Schedule Item", [$schedule_item]);

        if ($schedule_item->save()) {
            Log::info("Schedule Item created", [$schedule_item]);
        } else {
            Log::error("Error create schedule item", [$schedule_item]);
        }
    }

    public function delete(Request $request)
    {
        $schedule = Schedule::find($request->id);
        if ($schedule->delete()) {
            Log::info("Schedule deleted", [$schedule]);
            return response()->json([
                "message" => "Agendamento deletado"
            ], 200);
        } else {
            Log::error("Error delete schedule", [$schedule]);
            return response()->json([
                "message" => "Erro ao deletar agendamento"
            ], 400);
        }
    }

    public function sendMessage($recipient)
    {
        $recipient = "+55" . $recipient;
        $message = "Olá, você tem um novo agendamento para o dia 23/01/2024 às 10:00. Confirme seu agendamento em: https://www.google.com.br";
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create(
            $recipient,
            ['from' => $twilio_number, 'body' => $message]
        );
    }
    public function confirmationScheduleMessage(Request $request)
    {
        Log::info("Received schedule to confirmed", [$request]);
        $shedule = Schedule::where('confirmed_hash', $request->confirmed_hash)->firstOrFail();
        $shedule->confirmed = true;
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

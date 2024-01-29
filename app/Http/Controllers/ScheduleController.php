<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleItem;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function getSheduleFromEmployee(Request $request)
    {
        //receive  date and company_id from request and return shedules per employee
        $schedule = Schedule::with("scheduleItems", "client")->where("employee_id", $employee_id)->paginate(10);
        return response()->json([
            "data" => $schedule
        ], 200);
    }


    public function getSheduleFromCliente($client_id)
    {
        $schedule = Schedule::with("scheduleItems.employee")->where("client_id", $client_id)->paginate(10);
        return response()->json([
            "message" => "Agenda do cliente"
        ], 200);
    }

    public function getSchedulesFromEmployeesBeginningToday($employee_id)
    {
        Log::info("Searching schedules from employee");
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
            'employee_id' => 'required|max:255',
            'company_id' => 'required|max:255',
            'client_id' => 'required|max:255',
            'date' => 'required',
        ]);
        $schedule->confirmed_hash = Hash::make($request->company_id + $request->employee_id + Carbon::now());
        $schedule = Schedule::create($request->all());
        if ($schedule->save()) {
            if ($request->services) {
                foreach ($request->services as $service) {
                    $this->createScheduleItems($schedule->id, $service);
                }
            }
            $sms_message = "Seu agendamento foi realizado para o dia " . date("d/m/Y", strtotime($schedule->date)) . " às " . date("H:i", strtotime($schedule->start_time));

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

    private function createScheduleItems($schedule_id, $service)
    {
        Log::info("Update schedule item", [$service]);
        $schedule_item = ScheduleItem::find($service->id);
        $schedule_item->update([
            'schedule_id' => $schedule_id,
            'employee_id' => $service['employee_id'],
            'service_id' => $service['service_id'],
            'start_time' => $service['start_time'],
            'end_time' => $service['end_time'],
            'price' => $service['price'],
            'duration' => $service["duration"],
        ]);
        if ($schedule_item->save()) {
            Log::info("Schedule Item updated", [$schedule_item]);
        } else {
            Log::error("Error update schedule item", [$service]);
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
    public function confirmationScheudleMessage(Request $request)
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

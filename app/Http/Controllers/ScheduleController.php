<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleItem;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Mail;

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
            $client = Client::find($request->client_id);
            $company = Company::find($request->company_id);
            $data = [
                'name' => $client->name,
                'date' => date("d/m/Y", strtotime($schedule->date)),
                'start_time' => date("H:i", strtotime($schedule->start_time)),
                'company' => $company->name,
            ];
            Mail::send('mails.agendamento', $data, function($message){
                $message->to($client->email);
                $message->subject('Skedyou - Agendamento efetuado com sucesso!');
                $message->from('suporte@skedyou.com','Equipe Skedyou'); 
            });

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

    public function getAllSchedulesTodayToSentMessage()
    {
        $schedules = Schedule::with('client')->where("date", date("Y-m-d"))->where("confirmed", false)->get();
        $message = 'Olá, ' . $schedule->client->name . ' seu agendamento para o dia ' . date("d/m/Y", strtotime($schedule->date)) . ' às ' . date("H:i", strtotime($schedule->start_time)) . ' foi confirmado? Responda 1 para Sim ou 2 para Não.';
        foreach ($schedules as $schedule) {
            $this->sendMessage($schedule, $message);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleItem;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function getSheduleFromEmployee($employee_id)
    {
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
        $schedule = Schedule::create($request->all());
        if ($schedule->save()) {
            if ($request->services) {
                foreach ($request->services as $service) {
                    $this->createScheduleItems($schedule->id, $service);
                }
            }

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
}

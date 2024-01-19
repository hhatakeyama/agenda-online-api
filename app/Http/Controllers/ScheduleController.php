<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Schedule_Item;

class ScheduleController extends Controller
{
    public function getSheduleFromEmployee($employee_id)
    {
        $schedule = Schedule::with("scheduleItems", "client")->where("employee_id", $employee_id)::paginate(10)->get();
        return response()->json([
            "data" => $schedule
        ], 200);
    }

    public function getSheduleFromCliente($client_id)
    {
        $schedule = Schedule::with("scheduleItems.employee")->where("client_id", $client_id)::paginate(10)->get();
        return response()->json([
            "message" => "Agenda do cliente"
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
        if($schedule->save()) {
            if($request->services) {
                foreach($request->services as $service) {
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
        $schedule_item = Schedule_Item::find($service->id);
        $schedule_item = Schedule_Item::update([
            'schedule_id' => $schedule_id,
            'employee_id' => $service['employee_id'],
            'service_id' => $service['service_id'],
            'start_time' => $service['start_time'],
            'end_time' => $service['end_time'],
            'price' => $service['price'],
            'duration' => $service["duration"],
        ]);
        if($schedule_item->save()){
            Log::info("Schedule Item updated", [$schedule_item]);
        } else {
            Log::error("Error update schedule item", [$service]);
        }
    }

    public function update(Request $request)
    {
        $shedule = Schedule::find($request->id);
        $shedule->fill($request->all());
        if($shedule->save()){
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

    private function updateScheduleItems($schedule_id, $service)
    {
        Log::info("Schedule Item", [$service]);

        if($newService->save()){
            Log::info("Cchedule Item created", [$schedule_item]);
        } else {
            Log::error("Error create schedule item", [$service]);
        }
    }

    public function delete(Request $request)
    {
        $shedule = Schedule::find($request->id);
        if($user->delete()){
            Log::info("Schedule deleted", [$shedule]);
            return response()->json([
                "message" => "Agendamento deletado"
            ], 200);
        } else {
            Log::error("Error delete schedule", [$shedule]);
            return response()->json([
                "message" => "Erro ao deletar agendamento"
            ], 400);
        }
    }
}
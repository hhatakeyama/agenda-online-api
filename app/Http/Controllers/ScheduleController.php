<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function getSheduleFromEmployee($employee_id)
    {
        $schedule = Schedule::where("employee_id", $employee_id)::paginate(10)->get();
        return response()->json([
            "data" => $schedule
        ], 200);
    }

    public function getSheduleFromCliente($client_id)
    {
        $schedule = Schedule::where("client_id", $client_id)::paginate(10)->get();
        return response()->json([
            "message" => "Agenda do cliente"
        ], 200);
    }

    public function create(Request $request)
    {
        return response()->json([
            "message" => "Agendamento criado"
        ], 200);
    }

    public function update(Request $request)
    {
        return response()->json([
            "message" => "Agendamento atualizado"
        ], 200);
    }

    public function delete(Request $request)
    {
        return response()->json([
            "message" => "Agendamento deletado"
        ], 200);
    }
}

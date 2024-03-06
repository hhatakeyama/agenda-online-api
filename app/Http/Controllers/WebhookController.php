<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Schedule;

class WebhookController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info("Call Webhook", [$request->all()]);
        $refer = $request->refer;
        $action = explode("|", $refer);
        if ($action[0] === "confirm") {
            try {
                $schedule = Schedule::with(["scheduleItems.employee", "scheduleItems.service", "company.organization"])
                    ->where("confirmed_hash", $action[1])
                    ->firstOrFail();
                $schedule->confirmed = "1";
                if ($schedule->save()) {
                    Log::info("Schedule confirmed", [$schedule]);
                    return response()->json(["message" => "Agendamento confirmado com sucesso!"], 200);
                } else {
                    Log::error("Schedule not saved", [$schedule]);
                    return response()->json(["message" => "Erro ao confirmar agendamento. Tente novamente mais tarde."], 500);
                }
            } catch (\Exception $e) {
                Log::error("Error confirming schedule", [$e->getMessage()]);
                return response()->json(["message" => "Agendamento não encontrado"], 403);
            }
        }
        
        Log::error("No action found");
        return response()->json(["message" => "Ação não existe"], 403);
    }
}

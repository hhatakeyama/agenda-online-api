<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Client;
use App\Models\Company;
use App\Models\Schedule;
use App\Models\ScheduleItem;
use App\Models\Service;
use App\Models\User;
use Exception;

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

    public function getSheduleFromClient(Request $request, $client_id)
    {
        Log::info("Searching client schedules" . $client_id, [$request]);
        $page = $request->page ? $request->page : 1;
        $pageSize = $request->page_size ? $request->page_size : 20;
        $schedules = Schedule::with(["scheduleItems.employee", "scheduleItems.service", "company.organization"])
            ->where("client_id", $client_id)
            ->orderBy("date", "DESC")
            ->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            "data" => $schedules
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

    function unavailables(Request $request)
    {
        $date = $request->date;
        $date = date("Y-m-d", strtotime($date));
        $company = $request->company;
        $employees = $request->employees ? explode(",", $request->employees) : [];
        Log::info("List all unavailable schedules", [$company]);

        // List all employees scheduled periods
        $employeesScheduledPeriods = ScheduleItem::with("schedule")
            ->whereHas("schedule", function ($query) use ($company, $date) {
                $query->where("company_id", $company)
                    ->where("date", $date)
                    ->where(function ($subquery) {
                        $subquery->where([["done", 0], ["canceled", 0]])->orWhere('done', 1);
                    });
            })
            ->whereIn("employee_id", $employees)
            ->get();

        // Return all ScheduleItems
        return response()->json([
            "data" => $employeesScheduledPeriods
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
        $scheduleData['date'] = date("Y-m-d", strtotime($request->date));
        $scheduleData['confirmed_hash'] = Hash::make($request->company_id + $request->employee_id + date('YmdHis'));
        $schedule = Schedule::create($scheduleData);
        $firstItem = $request->items[0];
        if ($schedule->save()) {
            if ($request->items) {
                foreach ($request->items as $item) {
                    $this->createScheduleItems($schedule->id, $item);
                }
            }
            $client = Client::where('id', $request->client_id)->firstOrFail();
            $company = Company::find($request->company_id);
            $firstItem = $request->items[0];
            $data = [
                'name' => $client->name,
                'date' => date("d/m/Y", strtotime($schedule->date)),
                'start_time' => $firstItem['start_time'],
                'company' => $company->name,
            ];
            Mail::send('mails.agendamento', $data, function ($message) use ($client) {
                $message->to($client->email);
                $message->subject('Skedyou - Agendamento efetuado com sucesso!');
                $message->from('suporte@skedyou.com', 'Equipe Skedyou');
            });
            // \App\Jobs\Sms::dispatch($schedule, $request->items);
            // \App\Jobs\Email::dispatch($schedule, $request->items);

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

    private function updateScheduleItems($schedule_id, $schedule_item)
    {
        Log::info("Schedule Item", [$schedule_item]);

        if ($schedule_item->save()) {
            Log::info("Schedule Item created", [$schedule_item]);
        } else {
            Log::error("Error create schedule item", [$schedule_item]);
        }
    }

    public function confirmSchedule(Request $request)
    {
        Log::info("Confirming schedule", [$request->hash]);
        try {
            $schedule = Schedule::with(["scheduleItems.employee", "scheduleItems.service", "company.organization"])
            ->where("confirmed_hash", $request->hash)
            ->firstOrFail();
            $schedule->confirmed = "1";
            if ($schedule->save()) {
                Log::info("Schedule confirmed", [$schedule]);
                return response()->json(["message" => "Agendamento confirmado com sucesso!"], 200);
            } else {
                Log::error("Schedule not saved", [$schedule]);
                return response()->json(["message" => "Erro ao confirmar agendamento. Tente novamente mais tarde."], 500);
            }
        } catch (Exception $e) {
            Log::error("Error confirming schedule", [$e->getMessage()]);
            return response()->json(["data" => $schedule], 403);
        }
    }
}

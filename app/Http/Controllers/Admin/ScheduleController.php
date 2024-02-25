<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Client;
use App\Models\Company;
use App\Models\Schedule;
use App\Models\ScheduleItem;

class ScheduleController extends Controller
{
    public function get(Request $request)
    {
        $allowedTypes = ['g', 'f'];
        Log::info("Searching all schedules", [$request->user()]);
        if ($request->user()) {
            $search = $request->search;
            $page = $request->page ? $request->page : 1;
            $pageSize = $request->page_size ? $request->page_size : 10;
            $orderBy = $request->order_by ? $request->order_by : 'date';
            $company = $request->company ? $request->company : '';
            $date = $request->date ? $request->date : '';
            $schedules = [];
            Log::info("Searching all schedules", [$request->user()]);
            if (in_array($request->user()->type, $allowedTypes)) {
                $schedules = Schedule::with("scheduleItems.employee", "scheduleItems.service", "client")
                    ->whereHas("client", function ($query) use ($search) {
                        $query->where("name", "LIKE", "%$search%");
                    })
                    ->whereHas("company", function ($query) use ($request) {
                        $query->where("organization_id", $request->user()->organization_id);
                    });
            } else {
                $schedules = Schedule::with("scheduleItems.employee", "scheduleItems.service", "client")
                    ->whereHas("client", function ($query) use ($search) {
                        $query->where("name", "LIKE", "%$search%");
                    });
                // if ($request->organization_id) {
                //     $schedules = $schedules->where("organization_id", $request->organization_id);
                // }
            }
            if ($company) {
                $schedules = $schedules->where("company_id", $company);
            }
            if ($date) {
                $schedules = $schedules->where("date", $date);
            }
            if ($orderBy) {
                $schedules = $schedules->orderBy($orderBy, "DESC");
            }
            return response()->json([
                "data" => $schedules->paginate($pageSize, ['*'], 'page', $page)
            ], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getCalendar(Request $request)
    {
        $allowedTypes = ['g', 'f'];
        Log::info("Searching all schedules", [$request->user()]);
        if ($request->user()) {
            $search = $request->search;
            $company = $request->company ? $request->company : '';
            $date = $request->date ? $request->date : '';
            $schedules = [];
            Log::info("Searching all schedules", [$request->user()]);
            if (in_array($request->user()->type, $allowedTypes)) {
                $schedules = ScheduleItem::with("employee", "service", "schedule.client")
                    ->whereHas("schedule.client", function ($query) use ($search) {
                        $query->where("name", "LIKE", "%$search%");
                    })
                    ->whereHas("schedule.company", function ($query) use ($request) {
                        $query->where("organization_id", $request->user()->organization_id);
                    });
            } else {
                $schedules = ScheduleItem::with("employee", "service", "schedule.client")
                    ->whereHas("schedule.client", function ($query) use ($search) {
                        $query->where("name", "LIKE", "%$search%");
                    });
                // if ($request->organization_id) {
                //     $schedules = $schedules->where("organization_id", $request->organization_id);
                // }
            }
            if ($company) {
                $schedules = $schedules->whereHas("schedule", function ($query) use ($company) {
                    $query->where("company_id", $company);
                });
            }
            if ($date) {
                $schedules = $schedules->whereHas("schedule", function ($query) use ($date) {
                    $query->where("date", $date);
                });
            }
            return response()->json(["data" => $schedules->get()], 200);
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function getById(Request $request, $id)
    {
        $allowedTypes = ['s', 'a', 'g'];
        Log::info("Searching schedule id", [$id, $request->user()]);
        if (in_array($request->user()->type, $allowedTypes)) {
            try {
                $schedule = null;
                if ($request->user()->type === 'g') {
                    $schedule = Schedule::with("scheduleItems")
                        // ->where("organization_id", $request->user()->organization_id)
                        ->where("id", $id)
                        ->firstOrFail();
                } else {
                    $schedule = Schedule::findOrFail($id);
                }
                return response()->json(["data" => $schedule], 200);
            } catch (\Exception $e) {
                Log::info("Schedule not found", [$id, $e->getMessage()]);
                return response()->json(["message" => "Agendamento não encontrada."], 403);
            }
        } else {
            Log::error("User without permission");
            return response()->json(["message" => "Unauthorized"], 401);
        }
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

    // public function getAllSchedulesTodayToSentMessage()
    // {
    //     $schedules = Schedule::with('client')->where("date", date("Y-m-d"))->where("confirmed", "0")->get();
    //     $message = 'Olá, ' . $schedule->client->name . ' seu agendamento para o dia ' . date("d/m/Y", strtotime($schedule->date)) . ' às ' . date("H:i", strtotime($schedule->start_time)) . ' foi confirmado? Responda 1 para Sim ou 2 para Não.';
    //     foreach ($schedules as $schedule) {
    //         $this->sendMessage($schedule, $message);
    //     }
    // }
}

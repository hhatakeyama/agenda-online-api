<?php

namespace App\Http\Controllers;

date_default_timezone_set("America/Sao_Paulo");

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Schedule;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;

class MailController extends Controller {
    // Send
    public function schedule(Request $request, $companyId, $email, $scheduleId) {
        Log::info("Send schedule email");

        $schedule = Schedule::findOrFail($scheduleId);
        $employees = new Collection;
        $employeesIds = new Collection;
        foreach ($schedule->scheduleItems as $scheduleItem) {
            $employeeId = $scheduleItem->employee_id;
            if (!$employeesIds->contains($employeeId)) {
                $employeesIds->add($scheduleItem->employee_id);
                $employees->add($scheduleItem->employee);
            }
        }
        $endereco = $schedule->company->address . ', ' . $schedule->company->number . ', ' . $schedule->company->district . ', ' . $schedule->company->city->name . '/' . $schedule->company->state;
        $data = [
            "schedule" => $schedule,
            "employees" => $employees,
            "endereco" => $endereco,
        ];
        Mail::send('mails.scheduled', $data, function ($message) use ($email) {
            $message->to($email);
            $message->subject('Skedyou - Agendamento efetuado com sucesso!');
            $message->from('naoresponda@skedyou.app', 'Equipe Skedyou');
        });

        return "Schedule e-mail sent";
    }

    public function confirmationEmail(Request $request, $scheduleId) {
        Log::info("Send confirmation email");
        try {
            $schedule = Schedule::with("client", "scheduleItems")->findOrFail($scheduleId);
            \App\Jobs\EmailConfirmation::dispatch($schedule);
            return "Confirmation e-mail sent";
        } catch (\Exception $e) {
            return "Error sending confirmation e-mail. " . $e->getMessage();
        }
    }

    public function confirmationSms(Request $request, $scheduleId) {
        Log::info("Send confirmation SMS");
        try {
            $schedule = Schedule::with("client", "scheduleItems")->findOrFail($scheduleId);
            $mobilePhoneTest = "17991323162";
            \App\Jobs\Sms::dispatch($schedule, $mobilePhoneTest);

            return "Confirmation SMS sent";
        } catch (\Exception $e) {
            return "Error sending confirmation SMS. " . $e->getMessage();
        }
    }

    // Previews
    public function previewClientRegistration(Request $request) {
        $client = Client::orderBy("id", "DESC")->first();
        return new \App\Mail\ClientRegistration($client);
    }

    public function previewUserRegistration(Request $request) {
        $user = User::orderBy("id", "DESC")->first();
        return new \App\Mail\UserRegistration($user);
    }

    public function previewEmployeeRegistration(Request $request) {
        $user = User::orderBy("id", "DESC")->first();
        return new \App\Mail\EmployeeRegistration($user);
    }

    public function previewScheduleRegistration(Request $request) {
        $schedule = Schedule::orderBy("id", "DESC")->first();
        $employees = new Collection;
        $employeesIds = new Collection;
        foreach ($schedule->scheduleItems as $scheduleItem) {
            $employeeId = $scheduleItem->employee_id;
            if (!$employeesIds->contains($employeeId)) {
                $employeesIds->add($scheduleItem->employee_id);
                $employees->add($scheduleItem->employee);
            }
        }
        $endereco = $schedule->company->address . ', ' . $schedule->company->number . ', ' . $schedule->company->district . ', ' . $schedule->company->city->name . '/' . $schedule->company->state;
        return new \App\Mail\ScheduleRegistration($schedule, $employees, $endereco);
    }

    public function previewScheduleConfirmation(Request $request) {
        $schedule = Schedule::orderBy("id", "DESC")->first();
        $employees = new Collection;
        $employeesIds = new Collection;
        foreach ($schedule->scheduleItems as $scheduleItem) {
            $employeeId = $scheduleItem->employee_id;
            if (!$employeesIds->contains($employeeId)) {
                $employeesIds->add($scheduleItem->employee_id);
                $employees->add($scheduleItem->employee);
            }
        }
        $endereco = $schedule->company->address . ', ' . $schedule->company->number . ', ' . $schedule->company->district . ', ' . $schedule->company->city->name . '/' . $schedule->company->state;
        return new \App\Mail\ScheduleConfirmation($schedule, $employees, $endereco);
    }

    public function previewScheduleReminder(Request $request) {
        $schedule = Schedule::orderBy("id", "DESC")->first();
        $endereco = $schedule->company->address . ', ' . $schedule->company->number . ', ' . $schedule->company->district . ', ' . $schedule->company->city->name . '/' . $schedule->company->state;
        return new \App\Mail\ScheduleReminder($schedule, $endereco);
    }

    public function previewScheduleCancelation(Request $request) {
        $schedule = Schedule::orderBy("id", "DESC")->first();
        return new \App\Mail\Unschedule($schedule);
    }

    public function previewReschedule(Request $request) {
        $schedule = Schedule::orderBy("id", "DESC")->first();
        $employees = new Collection;
        $employeesIds = new Collection;
        foreach ($schedule->scheduleItems as $scheduleItem) {
            $employeeId = $scheduleItem->employee_id;
            if (!$employeesIds->contains($employeeId)) {
                $employeesIds->add($scheduleItem->employee_id);
                $employees->add($scheduleItem->employee);
            }
        }
        $endereco = $schedule->company->address . ', ' . $schedule->company->number . ', ' . $schedule->company->district . ', ' . $schedule->company->city->name . '/' . $schedule->company->state;
        return new \App\Mail\Reschedule($schedule, $employees, $endereco);
    }
}

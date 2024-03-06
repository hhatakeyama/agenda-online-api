<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Schedule;
use App\Models\Company;

class MailController extends Controller
{
    public function schedule(Request $request, $companyId, $email, $scheduleId)
    {
        Log::info("Send schedule email");
        $schedule = Schedule::with("client", "scheduleItems")->findOrFail($scheduleId);
        $company = Company::find($companyId);
        $data = [
            'name' => $schedule->client->name,
            'date' => date("d/m/Y", strtotime($schedule->date)),
            'start_time' => $schedule->scheduleItems[0]->start_time,
            'company' => $company->name,
        ];
        Mail::send('mails.agendamento', $data, function ($message) use ($email) {
            $message->to($email);
            $message->subject('Skedyou - Agendamento efetuado com sucesso!');
            $message->from('suporte@skedyou.com', 'Equipe Skedyou');
        });

        return "Schedule e-mail sent";
    }

    public function confirmationEmail(Request $request, $scheduleId)
    {
        Log::info("Send confirmation email");
        try {
            $schedule = Schedule::with("client", "scheduleItems")->findOrFail($scheduleId);
            \App\Jobs\Email::dispatch($schedule, $schedule->scheduleItems);
            return "Confirmation e-mail sent";
        } catch (\Exception $e) {
            return "Error sending confirmation e-mail. " . $e->getMessage();
        }
    }

    public function confirmationSms(Request $request, $scheduleId)
    {
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
}

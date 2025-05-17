<?php

namespace App\Console\Commands;

use App\Models\LogDisparo;
use App\Models\Schedule;
use Illuminate\Console\Command;

class ScheduleReminderCommand extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'emails:schedule-reminder-command';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send schedule reminder e-mail';

  /**
   * Execute the console command.
   */
  public function handle() {
    $today = date("Y-m-d");
    $twoDays = date("Y-m-d", strtotime($today . ' + 2 days'));
    $this->info('Send schedule reminder e-mail: ', [$today, $twoDays]);

    $schedulesTwoDays = Schedule::with("company", "client", "scheduleItems.service", "scheduleItems.employee")
      ->where("date", $twoDays)
      ->where("confirmed", "0")
      ->where("canceled", "0")
      ->where("done", "0")
      ->get();
    foreach ($schedulesTwoDays as $schedule) {
      \App\Jobs\SendEmailReminderJob::dispatch($schedule);

      LogDisparo::create([
        'tipo' => 'email',
        'destinatario' => $schedule->client->email,
        'assunto' => "Skedyou - Lembrete de agendamento",
        'mensagem' => json_encode(""),
        'status' => 'agendado',
        'data_agendamento' => now()
      ]);
      $this->info('Sending reminder', [$schedule->client]);
    }
    $this->info('Disparo de e-mails de lembrete de agendamento concluído.');
  }
}

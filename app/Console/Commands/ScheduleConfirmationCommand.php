<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\LogDisparo;
use App\Models\Schedule;

class ScheduleConfirmationCommand extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'emails:schedule-confirmation-command';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send schedule confirmation e-mail';

  /**
   * Execute the console command.
   */
  public function handle() {
    $today = date("Y-m-d");
    $tomorrow = date("Y-m-d", strtotime($today . ' + 1 days'));
    $twoDays = date("Y-m-d", strtotime($today . ' + 2 days'));
    $this->info('Send schedule confirmation e-mail: ', [$today, $tomorrow, $twoDays]);

    $schedules = Schedule::with("client", "scheduleItems")
      ->where("date", $tomorrow)
      ->where("confirmed", "0")
      ->where("canceled", "0")
      ->where("done", "0")
      ->get();
    foreach ($schedules as $schedule) {
      // \App\Jobs\SendSmsJob::dispatch($schedule);
      \App\Jobs\SendEmailConfirmationJob::dispatch($schedule);

      LogDisparo::create([
        'tipo' => 'email',
        'destinatario' => $schedule->client->email,
        'assunto' => json_encode("Skedyou - Confirmação de agendamento"),
        'mensagem' => json_encode(""),
        'status' => 'agendado',
        'data_agendamento' => now()
      ]);

      $this->info('Sending confirmation', [$schedule->client]);
    }
    $this->info('Disparo de e-mails de confirmação de agendamento concluído.');
  }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogDisparo;
use App\Models\User;

class AgendarEmailsDiarios extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'mails:daily';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Enfileira o envio de e-mails diários para os clientes.';

  /**
   * Execute the console command.
   */
  public function handle() {
    $users = User::where('receive_notifications', true)->get();
    foreach ($users as $user) {
      $dadosEmail = [
        'assunto' => 'Seu resumo diário',
        'conteudo' => 'Confira as novidades do dia!',
        // 'usuario_nome' => $usuario->nome,
      ];
      // EnviarEmail::dispatch($usuario->email, $dadosEmail);
      LogDisparo::create([
        'tipo' => 'email',
        // 'destinatario' => $usuario->email,
        'assunto' => "Seu resumo diário",
        'mensagem' => json_encode($dadosEmail),
        'status' => 'agendado',
        'data_agendamento' => now()
      ]);
      // $this->info("E-mail agendado para: {$usuario->email}");
    }
    $this->info('Agendamento de e-mails diários concluído.');
  }
}

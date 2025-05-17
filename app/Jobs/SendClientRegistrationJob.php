<?php

namespace App\Jobs;

use App\Models\LogDisparo;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendClientRegistrationJob implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $client;
  protected $password;

  /**
   * Create a new job instance.
   */
  public function __construct($client, $password) {
    $this->client = $client;
    $this->password = $password;
  }

  /**
   * Execute the job.
   */
  public function handle(): void {
    Log::info('[Job] Sending client registration e-mail', [$this->client, $this->password]);

    Mail::send('mails.cadastro', [
      'name' => $this->client->name,
      'password' => $this->password
    ], function ($message) {
      $message->to($this->client->email);
      $message->subject('Skedyou - Cadastro efetuado com sucesso!');
      $message->from('naoresponda@skedyou.app', 'Equipe Skedyou');
    });
  }
}

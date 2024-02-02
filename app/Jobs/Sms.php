<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Sms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //remove possiveis caracteres especiais
        // $telefone = preg_replace('/[^0-9]+/i', '', $this->schedule->telefone);

        // //o numero precisa ser de celular e o horario de agendamento precisa estar preenchido
        // if ((strlen($telefone) != 11) || (!$this->schedule->data_voucher || empty($this->schedule->data_voucher)))
        //     return 'nope';

        // $dataVoucher = date('d/m/Y', strtotime($this->schedule->data_voucher)); // data formatada 01/01 as 00:00
        // $periodoVoucher = ($this->schedule->promocao->id == 5 && in_array($this->schedule->unidade->id, [2, 12]) ? 'por ordem de chegada' : str_replace("à", "a", $this->schedule->periodo->nome));
        // $unidadeTelefone = (isset($this->schedule->unidade->telefone) && filled($this->schedule->unidade->telefone)) ? $this->schedule->unidade->telefone : false;
        // $corpo = "Agendado! Seu Voucher Fácil: {$this->schedule->voucher} - {$dataVoucher} - {$periodoVoucher}. {$this->schedule->promocao->cliente->razaoSocial} {$this->schedule->unidade->nome} ({$this->schedule->unidade->endereco}, {$this->schedule->unidade->numero})";

        // $client = new \GuzzleHttp\Client();

        // $data = http_build_query(array(
        //     'operacao' => 'ENVIO',
        //     'usuario' => 'web@p9.digital.com.br',
        //     'senha' => 'p9digital',
        //     'tipo' => 'SMS',
        //     'destino' => "55{$telefone}", //precisa de codigo internacional
        //     'mensagem' => "{$corpo}",
        //     'rota' => 'PREMIO'
        // ));

        // //chamada para a api
        // $response = $client->get("http://www.mmcenter.com.br/MMenvio.aspx?{$data}");
    }
}

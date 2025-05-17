<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDisparo extends Model {
  protected $fillable = [
    "tipo",
    "destinatario",
    "assunto",
    "mensagem",
    "status",
    "data_agendamento",
    "data_envio",
  ];
}

@extends('mails.layout')
@section('content')
  <p style="text-align: justify;">Olá, {{$client->name}}.</p>

  <p style="text-align: justify;">
    Seu agendamento foi realizado para o dia {{$schedule->date}} às {{$schedule->scheduleItem->start_time}} na unidade {{$company->name}}.<br><br>
    Obrigado por utilizar a Skedyou.
  </p>
@endsection

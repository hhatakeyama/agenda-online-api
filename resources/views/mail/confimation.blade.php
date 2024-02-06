@extends('mails.layout')
@section('content')
  <p style="text-align: justify;">Olá, {{$schedule->client->name}}.</p>

  <p style="text-align: justify;">
    Você possui um agendamento realizado  para o dia {{$schedule->date}} às {{$schedule->scheduleItem->start_time}} na unidade {{$schedule->company->name}}.<br><br>

    Para confirmar o agendamento, acesso o link abaixo:<br>
    <a href="url com o hash de confirmação">Confirmar Agendamento</a><br><br>
    Obrigado por utilizar a Skedyou.
  </p>
@endsection
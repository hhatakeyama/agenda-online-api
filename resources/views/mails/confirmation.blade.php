@extends('mails.layout')
@section('content')
  <p style="text-align: justify;">Olá, {{$schedule->client->name}}.</p>

  <p style="text-align: justify;">
    Você possui um agendamento realizado para o dia {{$schedule->date}} na unidade {{$schedule->company->name}}.<br /><br />
    @foreach($schedule->scheduleItems as $scheduleItem)
      -----------<br />
      {{$scheduleItem->service->name}} das {{$scheduleItem->start_time}} às {{$scheduleItem->end_time}} com {{$scheduleItem->employee->name}}<br />
      @if($scheduleItem->service->email_message)
        {!!nl2br($scheduleItem->service->email_message)!!}<br />
      @endif
    @endforeach
    -----------<br /><br />

    Para confirmar o agendamento, acesso o link abaixo:<br />
    <a href="{{$confirmationUrl}}">Confirmar Agendamento</a><br /><br />
    Obrigado por utilizar a Skedyou.
  </p>
@endsection
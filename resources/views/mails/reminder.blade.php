@extends('mails.layout')
@section('content')
<div style="text-align:center;font-size:22px;color:#333;font-weight:bold;margin-bottom:10px;">Lembrete de atendimento</div>
<p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">Olá, {{$schedule->client->name}}.</p>

<p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">
  Estamos passando para lembrar do seu agendamento realizado para o dia <strong>{{date("d/m/Y", strtotime($schedule->date))}}</strong> às <strong>{{$schedule->scheduleItems[0]->start_time}}h</strong>, local <strong>{{$schedule->company->organization->tradingName}}, unidade {{$schedule->company->name}}</strong>
</p>

@foreach ($schedule->scheduleItems as $scheduleItem)
<div style="background:#f7f7f7;border-radius:10px;padding:16px;font-size:15px;color:#444;margin-bottom:24px;">
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Serviço:</span> {{$scheduleItem->service->name}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Data:</span> {{date("d/m/Y", strtotime($schedule->date))}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Horário:</span> {{$scheduleItem->start_time}} às {{$scheduleItem->end_time}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Profissional:</span> {{$scheduleItem->employee->name}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Local:</span> {{$schedule->company->name}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Endereço:</span> {{$endereco}}</p>
  @if($schedule->company->complement)
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Complemento:</span> {{$schedule->company->complement}}</p>
  @endif
  @if($scheduleItem->service->email_message)
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Observação:</span> {!!nl2br($scheduleItem->service->email_message)!!}</p>
  @endif
</div>
@endforeach
@endsection
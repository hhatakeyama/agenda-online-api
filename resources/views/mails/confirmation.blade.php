@extends('mails.layout')
@section('content')
<div style="text-align:center;font-size:22px;color:#333;font-weight:bold;margin-bottom:10px;">Confirmação de atendimento</div>
<div style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">Olá, {{$schedule->client->name}}.</div>
<div style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">
  Você possui um atendimento agendado para o dia <strong>{{date("d/m/Y", strtotime($schedule->date))}}</strong> às <strong>{{$schedule->scheduleItems[0]->start_time}}h</strong>, local <strong>{{$schedule->company->organization->tradingName}} unidade {{$schedule->company->name}}</strong>
</div>

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

<p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">
  Para confirmar o agendamento, acesse o link abaixo:
</p>
<a class="button" href="{{env('SITE_URL') . '/confirm-schedule?hash=' . $schedule->confirmed_hash}}" style="display:block;text-align:center;background-color:#f57842;color:white;text-decoration:none;padding:14px;border-radius:8px;font-weight:bold;font-size:15px;margin:24px auto;width:fit-content;" target="_blank">
  Confirmar Agendamento ✅
</a>
@endsection
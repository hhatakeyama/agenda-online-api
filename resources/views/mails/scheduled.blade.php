@extends('mails.layout')
@section('content')
<div style="text-align:center;font-size:22px;color:#333;font-weight:bold;margin-bottom:10px;">Novo agendamento confirmado</div>
<div style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">Esses são os detalhes do seu atendimento:</div>
<div style="text-align:center;gap:15px;margin-bottom:12px;width:100%;">
  @foreach ($employees as $employee)
  <div style="display:inline-block;text-align:center;margin-bottom:12px;padding:0 5px;max-width:33%;">
    @if($employee->picture)
    <img alt="{{$employee->name}}" src="{{env('APP_URL')}}/storage/users/original-{{$employee->picture}}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #eee;" />
    @else
    <img alt="{{$employee->name}}" src="{{env('APP_URL')}}/icon.png" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #eee;" />
    @endif
    <div style="text-align:center;font-size:16px;font-weight:bold;color:#333;margin-bottom:24px;">{{$employee->name}}</div>
  </div>
  @endforeach
</div>
@foreach ($schedule->scheduleItems as $scheduleItem)
<div style="background:#f7f7f7;border-radius:10px;padding:16px;font-size:15px;color:#444;margin-bottom:24px;">
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Serviço:</span> {{$scheduleItem->service->name}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Data:</span> {{date("d/m/Y", strtotime($schedule->date))}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Horário:</span> {{$scheduleItem->start_time}} às {{$scheduleItem->end_time}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Profissional:</span> {{$scheduleItem->employee->name}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Local:</span> {{$schedule->company->organization->tradingName}} unidade {{$schedule->company->name}}</p>
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Endereço:</span> {{$endereco}}</p>
  @if($schedule->company->complement)
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Complemento:</span> {{$schedule->company->complement}}</p>
  @endif
  @if($scheduleItem->service->email_message)
  <p style="margin:8px 0;"><span style="font-weight:bold;color:#222;">Observação:</span> {!!nl2br($scheduleItem->service->email_message)!!}</p>
  @endif
</div>
@endforeach
<a href="https://www.google.com/maps/place/{{$endereco}}" style="display:block;text-align:center;background-color:#f57842;color:white;text-decoration:none;padding:14px;border-radius:8px;font-weight:bold;font-size:15px;margin:24px auto;width:fit-content;" target="_blank">
  Como chegar no local 📍
</a>
@endsection
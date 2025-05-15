@extends('mails.layout')
@section('content')
<div style="text-align:center;font-size:22px;color:#333;font-weight:bold;margin-bottom:10px;">Agendamento cancelado</div>
<div style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">Olá, {{$schedule->client->name}}.</div>

<p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">
  Seu atendimento do dia {{date("d/m/Y", strtotime($schedule->date))}} às {{$schedule->scheduleItems[0]->start_time}}, local {{$schedule->company->organization->tradingName}} unidade {{$schedule->company->name}} foi <strong>cancelado</strong>.
</p>

@if($schedule->company->phone || $schedule->company->mobilePhone)
  <p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">
    Em caso de dúvida, entre em contato com a unidade.
  </p>
  @if($schedule->company->phone)
    <a class="button" href="tel:{{str_replace('(', '', str_replace(') ', '', str_replace('-', '', $schedule->company->phone)))}}" style="display:block;text-align:center;background-color:#f57842;color:white;text-decoration:none;padding:14px;border-radius:8px;font-weight:bold;font-size:15px;margin:24px auto;width:fit-content;" target="_blank">
      Entrar em contato ☎
    </a>
  @endif
  @if($schedule->company->mobilePhone)
    <a class="button" href="https://wa.me/55{{str_replace('(', '', str_replace(') ', '', str_replace('-', '', $schedule->company->mobilePhone)))}}?text=Olá, gostaria de saber sobre o cancelamento do meu atendimento" style="display:block;text-align:center;background-color:#f57842;color:white;text-decoration:none;padding:14px;border-radius:8px;font-weight:bold;font-size:15px;margin:24px auto;width:fit-content;" target="_blank">
      Entrar em contato 📱
    </a>
  @endif
@endif
@endsection
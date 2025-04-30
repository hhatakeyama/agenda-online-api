@extends('mails.layout')
@section('content')
  <p style="text-align: justify;">Olá, {{$name}}.</p>

  <p style="text-align: justify;">
    Seu atendimento do dia {{$date}} às {{$start_time}} na unidade {{$company}} foi cancelado.<br /><br />
    Obrigado por utilizar a Skedyou.
  </p>
@endsection

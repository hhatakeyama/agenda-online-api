@extends('mails.layout')
@section('content')
  <p style="text-align: justify;">Olá, {{$name}}.</p>

  <p style="text-align: justify;">
    Seu atendimento foi reagendado para o dia {{$date}} às {{$start_time}} na unidade {{$company}}.<br /><br />
    Obrigado por utilizar a Skedyou.
  </p>
@endsection

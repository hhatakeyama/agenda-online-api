@extends('mails.layout')
@section('content')
  <p style="text-align: justify;">Olá, {{$name}}.</p>

  <p style="text-align: justify;">
    Seu cadastro foi realizado com sucesso<br /><br />
    Você pode acessar o painel e começar a utilizar nossos serviços pelo link abaixo:
    <a href="{{env('ADMIN_URL')}}">Acessar painel</a>
    Obrigado!
  </p>
@endsection

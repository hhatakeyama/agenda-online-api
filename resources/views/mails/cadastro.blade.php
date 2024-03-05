@extends('mails.layout')
@section('content')
  <p style="text-align: justify;">Olá, {{$name}}.</p>

  <p style="text-align: justify;">
    Seu cadastro foi realizado com sucesso<br /><br />
    Obrigado por se cadastrar na Skedyou. Agora você pode acessar nosso site e começar a utilizar nossos serviços.
  </p>
@endsection

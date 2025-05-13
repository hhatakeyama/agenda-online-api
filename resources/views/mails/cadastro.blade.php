@extends('mails.layout')
@section('content')
<div style="text-align:center;font-size:22px;color:#333;font-weight:bold;margin-bottom:10px;">Novo cadastro</div>
<p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">Olá, {{$name}}.</p>

<p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">
  Seu cadastro foi realizado com sucesso
</p>

@if ($password)
<p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">
  Sua senha temporária é: {{$password}}
</p>
@endif

<p style="text-align:center;font-size:16px;color:#666;margin-bottom:24px;">
  Obrigado por se cadastrar na Skedyou. Agora você pode acessar nosso site e começar a utilizar nossos serviços.
</p>

<a class="button" href="{{env('SITE_URL')}}" style="display:block;text-align:center;background-color:#f57842;color:white;text-decoration:none;padding:14px;border-radius:8px;font-weight:bold;font-size:15px;margin:24px auto;width:fit-content;" target="_blank">
  Acessar o site 📖
</a>
@endsection
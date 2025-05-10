<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>

<body style="font-family:Arial,sans-serif;background:#f6f6f6;margin:0;padding:20px;">
	<div class="container" style="max-width:540px;margin:auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 0 8px rgba(0,0,0,0.05);overflow:hidden;">
		<div class="logo" style="text-align:center;margin-bottom:20px;">
			<a href="{{env('SITE_URL')}}/planos" target="_blank">
				<img alt="Skedyou" src="{{env('APP_URL')}}/logo.png" style="width:120px;" />
			</a>
		</div>
		@yield('content')
		<div class="footer" style="font-size:12px;text-align:center;color:#999;padding-top:10px;">
			<p>Quer usar o Skedyou no seu negócio?<br />
				<a href="{{env('SITE_URL')}}/minha-conta/cadastro" style="color:#f57842;text-decoration:none;">Crie sua conta agora</a>
			</p>
			<div class="social-icons" style="margin:16px 0 0;display:flex;justify-content:center;gap:16px;">
				<a href="https://www.instagram.com/skedyou" style="color:#f57842;text-decoration:none;" target="_blank">
					<img alt="Instagram" src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" style="width:24px;height:24px;filter:brightness(0);opacity:0.8;transition:0.3s;" />
				</a>
				<a href="https://www.facebook.com/skedyou" style="color:#f57842;text-decoration:none;" target="_blank">
					<img alt="Facebook" src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" style="width:24px;height:24px;filter:brightness(0);opacity:0.8;transition:0.3s;" />
				</a>
				<a href="https://www.tiktok.com/@skedyou" style="color:#f57842;text-decoration:none;" target="_blank">
					<img alt="TikTok" src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/tiktok.svg" style="width:24px;height:24px;filter:brightness(0);opacity:0.8;transition:0.3s;" />
				</a>
				<a href="https://twitter.com/skedyou" style="color:#f57842;text-decoration:none;" target="_blank">
					<img alt="X" src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/x.svg" style="width:24px;height:24px;filter:brightness(0);opacity:0.8;transition:0.3s;" />
				</a>
				<a href="https://www.linkedin.com/company/skedyou" style="color:#f57842;text-decoration:none;" target="_blank">
					<img alt="LinkedIn" src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/linkedin.svg" style="width:24px;height:24px;filter:brightness(0);opacity:0.8;transition:0.3s;" />
				</a>
			</div>
		</div>
	</div>
</body>

</html>
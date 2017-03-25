<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gracias por activar tu cuenta</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"> 
	<link rel="stylesheet" href="<?php echo URL; ?>public/css/landing-page.css">
	<link rel="stylesheet" href="<?php echo URL; ?>public/css/landing-flipper.css">
	<!--favicons-->
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo FAV; ?>apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo FAV; ?>apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo FAV; ?>apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo FAV; ?>apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo FAV; ?>apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo FAV; ?>apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo FAV; ?>apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo FAV; ?>apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo FAV; ?>apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo FAV; ?>android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo FAV; ?>favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo FAV; ?>favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo FAV; ?>favicon-16x16.png">
	<link rel="manifest" href="/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">	
	<!--ZOPIM-->
	<!--Start of Zopim Live Chat Script-->
	<script type="text/javascript">
	window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
	d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
	_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
	$.src="//v2.zopim.com/?3iab5TpIzX50URoEgvzlU9tGfBw5CgHQ";z.t=+new Date;$.
	type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
	</script>
	<!--End of Zopim Live Chat Script-->
</head>
<body>

<section id="main">

	

	<!--Porqué Ecopulse-->

	<div class="container-fluid" id="por-que-ecopulse">

		<div class="container">

			<div class="row">

				<div class="col-md-4 col-md-offset-4">

					<p><a class="" href="<?php echo URL; ?>"><img alt="Ecopulse" src="<?php echo URL; ?>public/images/ecopulse-logo-150.png" width="150" ></a></p>
					<h1>Gracias por activar tu cuenta</h1>

					<h2>Estás un paso más cerca de comenzar a ahorrar en tus cuentas de electricidad</h2>
					<p>Por favor, inicia tu sesión</p>
					<div class="formWrapper" id="signInForm">
						<form action="<?php echo URL; ?>User/signIn/1" name="signIn" method="post">
							<div class="form-group"><input class="form-control" type="text" name="correo_us" id="correo_us_in" placeholder="Tu correo" required></div>
							<div class="form-group"><input class="form-control" type="password" name="contrasena_us" id="contrasena_us_in" placeholder="Contraseña" required></div>
							<div class="form-group"><input class="form-control btn btn-warning" type="submit" id="signInSubmit2" name="signInSubmit" value="Entrar"></div>
							<input type="hidden" name="primera" value="1">
							<!--<div id="signUpToggler">Regístrate</div>
							<div id="recordarToggler">Recordar contraseña</div>-->
						</form>
					</div>
			
				</div>

			</div>			

		</div>

	</div>

</section>
	
</body>
</html>

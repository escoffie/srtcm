<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo CL_NOMBRE; ?>: Aviso de privacidad</title>
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
</head>
<body style="margin-top:60px;">
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header pull-left">
				<img alt="<?php echo CL_NOMBRE; ?>" src="<?php echo URL; ?>public/images/ecopulse-logo-150.png" width="120" style="margin-left:4px; margin-top:8px;">
			</div>
			<div class="navbar-header pull-right">
				<ul class="nav pull-left">
					<li class="pull-right btnCerrar"><a href="#cerrar"><span class="fa fa-times-circle-o fa-2x"></span></a></li>
				</ul>
			</div>
		</div>
	</nav>

	<section class="main">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1>Aviso de privacidad</h1>
					<h2>Su privacidad es importante para nosotros</h2>
					<p><?php echo CL_NOMBRE; ?> recaba cierta información del usuario, proporcionada voluntariamente por éste, como nombre, teléfono, correo electrónico, dirección, hábitos de consumo de energía eléctrica, entre otros.</p>
					<p><?php echo CL_NOMBRE; ?> nunca proporcionará sus datos a terceros. Se mantendrán almacenados en nuestras bases de datos. Si no desea mantener los beneficios de su cuenta <?php echo CL_NOMBRE; ?>, puede socilitar a través del formulario de contacto que se elimine su información personal. <?php echo CL_NOMBRE; ?> tiene empresas afiliadas que podrían llegar a acceder a su información de contacto, pero nunca la compartirán con terceros.</p>
					<p><?php echo CL_NOMBRE; ?> y sus empresas afiliadas pueden utilizar su información de contacto para contactarle de vez en cuando para ofrecerle productos o servicios nuevos, o bien para solicitarle retroalimentación de sus productos o servicios actuales.</p>
					<p><?php echo CL_NOMBRE; ?> actúa proactivamente para resguardar su información, y cuenta con un capacitado equipo humano y sofisticados recursos técnicos para mantenerla segura. Sin embargo, en el evento de un robo de información, <?php echo CL_NOMBRE; ?> le informará tan pronto sea posible, mas no asumirá responsabilidad legal alguna.</p>
					<p><a href="#cerrar" class="btnCerrar btn btn-warning">Cerrar</a></p>
				</div>
			</div>
		</div>
	</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script >
	$(function(){
		var cerrar = $(".btnCerrar");
		cerrar.click(function(e){
			e.preventDefault();
			window.close();
		});
	});
</script>
	
</body>
</html>
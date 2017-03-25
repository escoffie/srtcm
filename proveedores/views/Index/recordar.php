<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo CL_NOMBRE; ?>: Recordar Contraseña</title>
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
    <script src='https://www.google.com/recaptcha/api.js?hl=es'></script>
</head>
<body style="margin-top:60px;">
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header pull-left">
				<img alt="<?php echo CL_NOMBRE; ?>" src="<?php echo URL; ?>public/images/logo.png" width="120" style="margin-left:4px; margin-top:8px;">
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
					<h1>Recuperar Contraseña</h1>




					<p>Podemos ayudarte a restablecer tu contraseña. Primero escribe tu correo electrónico y sigue las instrucciones siguientes.</p>
                    
                    
                    <div>
                    
                    
                    
                    <div class="row">
                        <div class="col-md-12" id="monitorFiltro"></div>
                    </div>

                    
                    
                    
                    <div class="col-md-4">
                    <div class="formWrapper" id="signInForm">
							<form name="signIn" method="post" id="confirma">
								<div class="form-group"><input class="form-control" type="text" name="usuario" id="usuario" placeholder="Tu correo" required></div>
                               <!-- <div class="g-recaptcha" data-sitekey="6LeugRgUAAAAAMNsDVh5-s1MtbtZ7_ZZ2PlJH1RU"></div>--> 
                             </form>
                         </div>
                         <p><a href="#cerrar" class="btnCerrar btn btn-warning">Cerrar</a></p>
					</div>
                    <a class="btnConfirmar btn btn-danger">Confirmar Correo</a>					
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
	
	//accion del botón confirmar correo
	$(document).on("click",".btnConfirmar",function(){
		var email = $("#usuario").val();
		var a = validarEmail( email );
		
		if(a==false){
			alert("Error: La dirección de correo " + email + " es incorrecta.");
			$("#usuario").focus();
			return false;
			}
		
		var data = new FormData();
		data.append('correo',$("#usuario").val());
		$.ajax({
				url:'<?php echo URL; ?>Index/confirmaCorreo', 
				type:'POST', 
				contentType:false, 
				data:data, 
				processData:false, 
				cache:false,
				
				}).done(function(msg){
					//alert(msg);
				if(msg==1){
					//alert('la cuenta de correo si existe');
					$("#monitorFiltro").html('').append('<div class="alert alert-success"><strong>Envio exitoso de la contraseña a su cuenta de correo....</strong></div>');
					}else{
						$("#monitorFiltro").html('').append('<div class="alert alert-danger"><strong>La cuenta de correo ingresada no existe en nuestro sistema.Favor de verificar....</strong></div>');
						}
			});
			
		});
		

//funcio que valida la cuenta de correo		
function validarEmail( email ) {
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if ( !expr.test(email) ){
        //
		return false;
		}else{
			return true;
			}
}

</script>
	
</body>
</html>
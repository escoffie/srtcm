<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'config.php';

$url = (isset($_GET['url'])) ? $_GET['url'] : 'Index/index';

$url = explode("/", $url);

//Controlador/método/parámetros
if(isset($url[0])) {$controller = $url[0];}
if(isset($url[1]) and $url[1] !='' ) {$method = $url[1];}
if(isset($url[2]) and $url[2] !='' ) {$params = $url[2];}

//Lazy load para cargar clases
spl_autoload_register( function($class) {

	//Sospecho que en virtud de esto es que no hay que incluir ninguna librería
	if(file_exists(LIBS.$class.'.php')){
		require_once LIBS.$class.'.php';
	}

});

//Lazy load de PHPMailer
require 'phpmailer/PHPMailerAutoload.php';

//Cargar e instanciar dinámicamente los controladores
$rutaControlador = './controllers/'.$controller.'.php';
if( file_exists($rutaControlador) ){
	require_once $rutaControlador;
	$controller = new $controller();

	//Si está definido un método por la URL, y si éste existe,
	//lo ejecuta, ya sea con o sin parámetros, según existan o no
	if( isset($method) ){
		if( method_exists($controller, $method) ){
			if( isset($params) ){
				$controller->{$method}($params);
			} else {
				$controller->{$method}();
			}
		}
	} else {
		//Si no hay definido un método, se ejecuta el método index() de la clase Index
		$controller->index();
	}

} else {
	echo "Controlador $controller no encontrado";
}

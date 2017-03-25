<?php
//Testing
$produccion = true; //default false
define('PRODUCCION', $produccion);

//Personalización
define('CL_NOMBRE', 'Surticoma');

/* =========== no mover nada bajo estas líneas ============*/

//Rutas fijas
if($produccion==false) {
	define('URL', 'http://escoffie.com/proveedores/');
} else {
	define('URL', 'http://surticoma.com/proveedores/');
}
define('IMG', URL.'public/images/');
define('IMGE', URL.'email-templates/images/');
define('FAV', URL.'public/favicons/');
define('BOWER', URL.'public/dashboard/');
define('LIBS', 'libs/');

//Debug
define('DEBUG', false);

if($produccion==false){
	//Database
	define('DB_HOST', 'localhost');
	define('DB_USER', 'surticom_admin');
	define('DB_PASS', 'Racatapulfuf1?');
	define('DB_NAME', 'surticom_crm');
} else {
	//Database
	define('DB_HOST', 'localhost');
	define('DB_USER', 'surticom_admin');
	define('DB_PASS', 'Racatapulfuf1?');
	define('DB_NAME', 'surticom_crm');
	
}

// Duración de la visita (en segundos)
define('DURACION', 1200); // 20 minutos

//Configuración local
date_default_timezone_set('America/Mexico_City');
setlocale(LC_ALL, 'es_MX');
setlocale(LC_NUMERIC, 'en_US');

//Correo electrónico
define('REMITENTENOMBRE', 'CRM Surticoma.');
define('REMITENTE', 'crm@surticoma.com');
define('USUARIO', 'crm@surticoma.com');
define('PASSWORD', 'Racatapulfuf1?');
define('SERVIDORSALIDA', 'mail.surticoma.com');
define('PUERTOSALIDA', 587);


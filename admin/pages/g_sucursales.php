<?php

//Categorías

$sucursales = Xcrud::get_instance();

$sucursales->connection('surticom_admin', 'Racatapulfuf1?', 'surticom_think', 'localhost', 'utf8');


$sucursales->table('geolocalizacion_geo');

$sucursales->order_by('estado_geo');
$sucursales->order_by('municipio_geo');
$sucursales->order_by('tienda_geo');

$sucursales->table_name('Sucursales', 'Gestión de sucursales para geolocalización. Captura la latitud y la longitud en decimales, no en grados.', 'icon-user');

$sucursales->label(array(
				'estado_geo' => 'Estado', 
				'municipio_geo' => 'Población',
				'tienda_geo' => 'Tienda',
				'telefono_geo' => 'Teléfono',
				'direccion_geo' => 'Dirección',
				'latitud_geo' => 'Latitud (decimal)',
				'longitud_geo' => 'Longitud (decimal)',
				
));

echo $sucursales->render();

?>
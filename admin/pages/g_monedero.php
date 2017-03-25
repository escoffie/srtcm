<?php

//Categorías

$comentarios = Xcrud::get_instance();

$comentarios->connection('surticom_admin', 'Racatapulfuf1?', 'surticom_think', 'localhost', 'utf8');

$comentarios->table('monedero_mo');

$comentarios->order_by('id_mo');

$comentarios->table_name('Monedero', 'Solicitudes de información sobre Monedero Willys enviadas desde el sitio web.', 'icon-user');

$comentarios->columns('nombre_mo, razon_mo, rfc_mo, email_mo, telefono_mo, estado_mo, municipio_mo, monto_mo, frecuencia_mo, fecha_mo');

$comentarios->label(array(
				'nombre_mo' => 'Nombre',
				'razon_mo' => 'Razón social',
				'rfc_mo' => 'RFC',
				'email_mo' => 'Email',
				'telefono_mo' => 'Teléfono',
				'estado_mo' => 'Estado',
				'municipio_mo' => 'Municipio',
				'monto_mo' => 'Monto',
				'frecuencia_mo' => 'Frecuencia',
				'fecha_mo' => 'Fecha',
				
));

echo $comentarios->render();

?>
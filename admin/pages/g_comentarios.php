<?php

//Categorías

$comentarios = Xcrud::get_instance();

$comentarios->connection('surticom_admin', 'Racatapulfuf1?', 'surticom_think', 'localhost', 'utf8');


$comentarios->table('contacto_co');

$comentarios->order_by('id_co', 'desc');

$comentarios->table_name('Contacto', 'Gestión de comentarios enviados desde el sitio web.', 'icon-user');

$comentarios->label(array(
				'nombre_co' => 'Nombre',
				'cumpleanos_co' => 'Cumpleaños',
				'email_co' => 'Email',
				'telefono_co' => 'Teléfono',
				'estado_co' => 'Estado',
				'mensaje_co' => 'Mensaje',
				'ip_co' => 'IP',
				'fecha_co' => 'Fecha',
				
));

echo $comentarios->render();

?>
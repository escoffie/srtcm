<?php

//Categorías

$inmuebles = Xcrud::get_instance();

$inmuebles->connection('surticom_admin', 'Racatapulfuf1?', 'surticom_think', 'localhost', 'utf8');

$inmuebles->table('inmuebles_in');

$inmuebles->order_by('id_in', 'desc');

$inmuebles->table_name('Inmuebles', 'Gestión de inmuebles propuestos por visitantes desde el sitio web.', 'icon-user');

$inmuebles->columns('nombre_in, estado2_in, municipio2_in, telefono_in, celular_in, fecha_in');

$inmuebles->label(array(
				'nombre_in' => 'Nombre',
				'email_in' => 'Email',
				'telefono_in' => 'Teléfono',
				'celular_in' => 'Celular',
				'estado_in' => 'Estado',
				'municipio_in' => 'Municipio',
				'direccion_in' => 'Cumpleaños',
				'estado2_in' => 'Estado predio',
				'municipio2_in' => 'Municipio predio',
				'frente_in' => 'Frente',
				'fondo_in' => 'Fondo',
				'transaccion_in' => 'Transacción',
				'precio_in' => 'Precio',
				'latitud_in' => 'Latitud',
				'longitud_in' => 'Longitud',
				'comentarios_in' => 'Mensaje',
				'fecha_in' => 'Fecha',
				'ip_in' => 'IP',
				
));

echo $inmuebles->render();

?>
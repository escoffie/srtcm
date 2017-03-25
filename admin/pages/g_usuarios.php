<?php

//Categorías

$usuarios = Xcrud::get_instance();

$usuarios->connection('surticom_admin', 'Racatapulfuf1?', 'surticom_think', 'localhost', 'utf8');


$usuarios->table('usuarios_us');
$usuarios->relation('level', 'usuarios_level', 'cod_level', 'nombre_level');

$usuarios->table_name('Usuarios', 'Gestión de usuarios', 'icon-user');

$usuarios->change_type('password', 'password', 'md5', array('placeholder'=>'Nueva contraseña'));

$usuarios->label(array(
				'user' => 'Usuario', 
				'password' => 'Contraseña',
				'level' => 'Nivel',
				'nombre' => 'Nombre completo',
				'puesto' => 'Puesto',
				'email' => 'Correo electrónico',
				'online' => 'Último acceso',
				
));

$usuarios->readonly('online', 'edit');
echo $usuarios->render();

?>
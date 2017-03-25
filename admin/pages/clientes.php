<?php
//Clientes
$clientes = Xcrud::get_instance();
$clientes->table('clientes_cl');
$clientes->table_name('Clientes', 'Información sobre clientes suscritos para compra en línea', 'icon-users-2');
$clientes->readonly('username_cl', 'edit');
$clientes->fields('fecha_cl', true, false, 'create');
$clientes->pass_var('fecha_cl', date('Y-m-d H:i:s'), 'create');
$clientes->validation_required('username_cl, password_cl, email_cl, nombre_cl, telefono1_cl');
//$clientes->alert_create('email_cl', 'escoffie@think-comunicacion.com', 'Su nueva cuenta', "Apreciable {nombre_cl},/r/nGracias por ser cliente de Pantuflamanía./r/nSu nombre de usuario es {username_cl} y su contraseña es {password_cl} y para acceder entre a Pantuflamanía.com./r/n¡Bienvenido!/r/n/r/nAtentamente,/r/nEduardo Elías Dájer Bechara/r/nDirector general");
$clientes->validation_pattern(array(
	'username_cl'=>'[a-zA-Z0-9]{5,20}',
	'email_cl'=>'email',
	'telefono1_cl' => 'numeric',
	'telefono2_cl' => 'numeric'
));
$clientes_labels = array(
	'username_cl'=>'Usuario',
	'password_cl'=>'Contraseña',
	'email_cl'=>'Correo electrónico',
	'nombre_cl'=>'Nombre completo',
	'telefono1_cl'=>'Teléfono 1',
	'fecha_cl'=>'Cliente desde',
	'estatus_cl'=>'Estado',
	'f_razonsocial_cl'=>'Razón social',
	'f_rfc_cl'=>'RFC',
	'f_direccion_cl'=>'Dirección fiscal',
);
$clientes->label($clientes_labels);
echo $clientes->render();
?>
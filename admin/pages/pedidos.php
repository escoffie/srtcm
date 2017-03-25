<?php
$pedidos = Xcrud::get_instance();
$pedidos->table('pedidos_pe');
$pedidos->table_name('Pedidos', 'Lista de pedidos. Clic para ver detalles de cada pedido', 'icon-cart');
$pedidos->order_by('fecha_pe', 'desc');
$pedidos->readonly('fecha_pe', 'edit');
$pedidos->relation('id_cl', 'clientes_cl', 'id_cl', 'nombre_cl');
$pedidos_labels = array(
	'id_cl' => 'Cliente',
	'fecha_pe' => 'Fecha del pedido',
	'estado_pe' => 'Estado del pedido'
);
$pedidos->label($pedidos_labels);

$productos = $pedidos->nested_table('Productos', 'id_pe', 'pedidos_detalle_pd', 'id_pe');
$productos->table_name('Productos', 'Lista de productos en este pedido','');
$productos->columns(array('codigo_pd', 'nombre_pd', 'observaciones_pd', 'precio_pd', 'cantidad_pd', 'Total'));
$productos_labels = array(
	'codigo_pd'				=>		'Código',
	'nombre_pd'				=>		'Nombre',
	'observaciones_pd'		=>		'Observaciones',
	'precio_pd'				=>		'Precio unit.',
	'cantidad_pd'			=>		'Cantidad'
);
$productos->label($productos_labels);
$productos->subselect('Total', '{cantidad_pd}*{precio_pd}');
$productos->sum('cantidad_pd, Total');
$productos->change_type('precio_pd','price','0',array('prefix'=>'$')); // number format
$productos->change_type('Total','price','0',array('prefix'=>'$')); // number format

$clientes = $pedidos->nested_table('Cliente', 'id_cl', 'clientes_cl', 'id_cl');
$clientes->table_name('Datos del cliente', '', '');
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
	'direccion_cl'=>'Dirección de envío',
	'telefono1_cl'=>'Teléfono 1',
	'telefono2_cl'=>'Teléfono 2',
	'fecha_cl'=>'Cliente desde',
	'estatus_cl'=>'Estado',
	'f_razonsocial_cl'=>'Razón social',
	'f_rfc_cl'=>'RFC',
	'f_direccion_cl'=>'Dirección fiscal',
);
$clientes->label($clientes_labels);
$clientes->unset_add();
$clientes->unset_remove();
$clientes->unset_pagination();

echo $pedidos->render();
?>
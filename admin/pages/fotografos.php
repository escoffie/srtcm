<?php
$fotografos = Xcrud::get_instance();
$fotografos->table('fotografos_fo');
$fotografos->table_name('Fotógrafos', 'Lista de fotógrafos colaboradores. Clic para ver fotos por fotógrafo', 'icon-camera');
$fotografos->order_by('fecha_fo', 'desc');
$fotografos->readonly('fecha_fo', 'edit');
//$fotografos->relation('id_fo', 'productos_pr', 'id_fo', 'nombre_pr');
$fotografos_labels = array(
	'fecha_fo' => 'Alta',
	'estado_fo' => 'Estado'
);
$fotografos->label($fotografos_labels);

$productos = $fotografos->nested_table('Productos', 'id_fo', 'productos_pr', 'id_fo');
$productos->table_name('Fotografías', 'Lista de imágenes proporcionadas por este colaborador','');
$productos->columns(array('id_pr', 'nombre_pr', 'descripcion_pr', 'fecha_pr', 'foto1_pr', 'estado_pr', 'ventas_pr'));
$productos_labels = array(
	'id_pr'				=>		'Código',
	'nombre_pr'			=>		'Nombre',
	'descripcion_pr'	=>		'Descripción',
	'fecha_pr'			=>		'Alta',
	'foto1_pr'			=>		'Foto',
	'estado_pr'			=>		'Estado',
	'ventas_pr'			=>		'Vendida',

);

echo $fotografos->render();
?>
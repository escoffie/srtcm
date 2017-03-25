<?php
//Productos
$productos = Xcrud::get_instance();
$productos->table('productos_pr');
$productos->table_name('Fotografías', 'Listado de fotografías disponibles para pedido en línea', 'icon-gift');
$productos->relation('id_fo', 'fotografos_fo', 'id_fo', 'nombre_fo');
$productos->relation('id_ca', 'categorias_ca', 'id_ca', 'categoria_ca', null, null, true);
$productos->fields('fecha_pr', true, false, 'create');
$productos->pass_var('fecha_pr', date('Y-m-d H:i:s'), 'create');
$productos->fields('estado_pr', true, false, 'create');
$productos->pass_var('estado_pr', 1, 'create');
$productos->label(array(
	'id_fo'=>'Fotógrafo',
	'id_ca'=>'Categoría(s)',
	'nombre_pr'=>'Nombre',
	'descripcion_pr'=>'Descripción',
	'observaciones_pr'=>'Palabras clave (separar por espacios)',
	'fecha_pr'=>'Fecha alta',
	'precio_pr'=>'Precio',
	'foto1_pr'=>'Imagen (máx. 12 Mb)',
	'ventas_pr'=>'Vendida',
	'estado_pr'=>'Estado'
));

$productos->change_type('descripcion_pr','textarea');
$productos->change_type('observaciones_pr','textarea');

$productos->change_type('foto1_pr','image','', array(
	/*'width'=>600, 'height'=>600,  */
	
	'thumbs'=>array(
		array(
			'watermark' => '../admin/assets/wm.png',
			'width'=>800,
			'height'=>800,
			'folder'=>'wm'
		), array(
			'watermark' => '../admin/assets/wm-chico.png',
			'width'=>400,
			'height'=>400,
			'folder'=>'thumbs'
		), array(
			'height'=>55,
			'width'=>55,
			'crop'=>false,
			'folder'=>'thumbsgrid'
		)
	),
	'grid_thumb'=>2,
	'detail_thumb'=>1)
);
$productos->change_type('precio_pr','price', '100', array('prefix'=>'$'));

//Vía callback en functions.php
$productos->create_action('publicar', 'publicar_producto'); 
$productos->create_action('ocultar', 'ocultar_producto');
$productos->button('#', 'Oculto', 'icon-close glyphicon glyphicon-remove', 'xcrud-action', array(
	'data-task'=>'action',
	'data-action'=>'publicar',
	'data-primary'=>'{id_pr}'), 
	array('estado_pr','!=','1')
);
$productos->button('#', 'Publicado', 'icon-checkmark glyphicon glyphicon-ok', 'xcrud-action', array(
	'data-task'=>'action',
	'data-action'=>'ocultar',
	'data-primary'=>'{id_pr}'), 
	array('estado_pr','=','1')
);

echo $productos->render();
?>
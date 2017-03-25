<?php

//Categorías

$categorias = Xcrud::get_instance();

$categorias->table('categorias_ca');

$categorias->table_name('Categorías', 'Lista de categorías a las que un producto puede pertenecer para facilitar la navegación', 'icon-tags');

$categorias->label(array(
				'categoria_ca' => 'Nombre de la categoría', 
				'fondo_ca' => 'Imagen de fondo'
));

$categorias->change_type('fondo_ca','image','', array(

	'width'=>1024, 'height'=>582, 'crop'=>false, 'thumbs'=>array(array(

		'height'=>360,

		'width'=>360,

		'crop'=>false,

		'folder'=>'thumbs'

	), array(

		'height'=>55,

		'width'=>55,

		'crop'=>false,

		'folder'=>'thumbsgrid'

	)), 'grid_thumb'=>1, 'detail_thumb'=>0)

);


echo $categorias->render();

?>
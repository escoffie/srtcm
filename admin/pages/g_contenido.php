<?php

//Categorías

$contenido = Xcrud::get_instance();

$contenido->connection('surticom_admin', 'Racatapulfuf1?', 'surticom_think', 'localhost', 'utf8');

$contenido->table('html');
$contenido->where('cod_lang =', '1');
$contenido->order_by('cod_parent', 'asc');
$contenido->order_by('orderby', 'desc');
$contenido->relation('cod_parent', 'html', 'cod_child', 'h1');
$contenido->relation('cod_lang', 'lenguajes_lang', 'cod_lang', 'nativename_lang');
$contenido->relation('cod_st', 'set_template_st', 'cod_st', 'name_st');
$contenido->relation('autor', 'usuarios_us', 'cod_user', 'nombre');

$contenido->table_name('Contenido', 'Gestión de contenidos', 'icon-gift');

$contenido->columns('cod_parent, cod_st, orderby, title, description, h1, creationDate, editDate, autor, url, publicar');
$contenido->fields('cod_parent, cod_st, orderby, title, description, h1, html, include_php, url, aux1, publicar');

$contenido->change_type('title', 'text');
$contenido->change_type('description', 'textarea');
$contenido->change_type('h1', 'text');
$contenido->change_type('html', 'texteditor');

//Al crear
$contenido->pass_default('cod_st', 3);
$contenido->pass_var('autor', $_SESSION['cod_user'], 'create');
$contenido->pass_var('creationDate', date('Y-m-d'), 'create');
$contenido->pass_var('creationTime', date('H:i:s'), 'create');
//Al editar
$contenido->pass_var('editedBy', $_SESSION['cod_user'], 'edit');
$contenido->pass_var('editDate', date('Y-m-d'), 'edit');
$contenido->pass_var('editTime', date('H:i:s'), 'edit');

$contenido->label(array(
				'cod_parent' => 'Nivel superior', 
				'cod_lang' => 'Idioma',
				'cod_st' => 'Plantilla',
				'orderby' => 'Orden',
				'title' => 'Título de ventana',
				'description' => 'Descripción',
				'keywords' => 'Palabras clave',
				'h1' => 'Encabezado',
				'html' => 'Contenido',
				'include_php' => 'Incluir script',
				'related' => 'Relacionado',
				'creationDate' => 'Fecha creación',
				'creationTime' => 'Hora creación',
				'editDate' => 'Fecha edición',
				'editTime' => 'Hora edición',
				'autor' => 'Autor',
				'editedBy' => 'Editor',
				'showdate' => 'Mostrar desde',
				'hidedate' => 'Mostrar hasta',
				'url' => 'URL',
				'promocion' => 'n',
				'swfurl' => 'n',
				'swfxsize' => 'n',
				'swfysize' => 'n',
				'enportada' => 'n',
				'aux1' => 'Auxiliar',
				'publicar' => 'Visible',
));

$contenido->readonly('cod_lang,creationdate,creationtime,editdate,edittime,promocion,', 'edit');

$fotografias = $contenido->nested_table('Fotografias', 'cod_child', 'files', 'cod_child');
$fotografias->connection('surticom_admin', 'Racatapulfuf1?', 'surticom_think', 'localhost', 'utf8');
$fotografias->table_name('Imágenes', 'Colección de imágenes vinculadas a la publicación', 'icon-gift');
$fotografias->columns('url_file, aux1');
$fotografias->fields('url_file, aux1');
$fotografias->change_type('url_file','image');
$fotografias->label(array(
	'url_file'=>'Imagen',
	'aux1'=>'Leyenda',
));


echo $contenido->render();

?>
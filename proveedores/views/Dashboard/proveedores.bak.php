<?php require ('./public/php/Dashboard_header.php'); ?>
<?php
$xcrud = Xcrud::get_instance();
$xcrud->table('proveedores_pro');
$xcrud->table_name('Proveedores');
$xcrud->where('id_rol<=',2);
$xcrud->columns('id_rol,avatar_pro',true);
$xcrud->unset_add();
$xcrud->unset_csv();
$xcrud->unset_remove();
$xcrud->label('id_rol', 'Rol');
//$xcrud->label('id_niv','Nivel de acceso');
$xcrud->label('estatus_pro','Estatus');
$xcrud->label('fecha_pro','Fecha de alta');
$xcrud->label('codigo_pro','Código interno');
$xcrud->label('rfc_pro','RFC');
$xcrud->label('razonsocial_pro','Razón social');
$xcrud->label('direccion_pro','Dirección');
$xcrud->label('telefono_pro','Teléfono');
$xcrud->label('email_pro','Email');
$xcrud->label('pw_pro','Contraseña');
$xcrud->readonly('codigo_pro,rfc_pro,razonsocial_pro');
$xcrud->fields('id_rol', true);
$xcrud->fields('fecha_pro', true);
$xcrud->fields('avatar_pro', true);
//$xcrud->change_type('id_niv','checkboxes');
$xcrud->change_type('fecha_pro','datetime');
$xcrud->change_type('pw_pro', 'password', 'md5', array('placeholder'=>'Nueva contraseña'));
$xcrud->validation_pattern('email_pro','email');
$xcrud->relation('id_rol', 'roles_rol', 'id_rol', 'nombre_rol');
//$xcrud->relation('id_niv', 'niveles_niv', 'id_niv', 'descripcion_niv');

//Sucursales
$suc = $xcrud->nested_table('Sucursales', 'codigo_pro', 'empresa_sucursal_proveedor_esp', 'codigo_pro');
$suc->table_name('Acceso a sucursales');
$suc->label(array(
	'codigo_sucursal_suc'=>'Sucursal',
	'estatus_esp' => 'Estatus',
	'id_niv' => 'Nivel de acceso',
));
$suc->columns('codigo_pro', true);
$suc->fields('codigo_pro', true);
//$suc->disabled('codigo_sucursal_suc');
$suc->relation('codigo_sucursal_suc', 'sucursales_suc', 'codigo_sucursal_suc', array('estado_suc','region_suc','nombre_suc', 'tipo_suc'),'','','',' - ');
$suc->relation('id_niv', 'niveles_niv', 'id_niv', array('nombre_niv','descripcion_niv'),'','','',': ');
$suc->change_type('estatus_esp', 'bool');
$suc->unset_remove();
$suc->unset_view();
$suc->unset_edit();
$suc->unset_add();
$suc->unset_csv();
$suc->unset_search();
$suc->unset_pagination();

// Botón de switcheo de booleano
	$suc->create_action('publish', 'publish_action'); // action callback, function publish_action() in functions.php
    $suc->create_action('unpublish', 'unpublish_action');
    $suc->button('#', 'Inactivo', 'icon-close glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', 
        array(  // set action vars to the button
            'data-task' => 'action',
            'data-action' => 'publish',
            'data-primary' => '{id_esp}'), 
        array(  // set condition ( when button must be shown)
            'estatus_esp',
            '!=',
            '1'
		)
    );
    $suc->button('#', 'Activo', 'icon-checkmark glyphicon glyphicon-ok-circle', 'xcrud-action btn-default', 
		array( // acción
			'data-task' => 'action',
			'data-action' => 'unpublish',
			'data-primary' => '{id_esp}'), 
		array( // condición
			'estatus_esp',
			'=',
			'1'
		)
	);
		
// Botón de switcheo de nivel de acceso
	$suc->create_action('a_basico', 'basico_action'); // action callback, function publish_action() in functions.php
    $suc->create_action('a_premium', 'premium_action');
    $suc->button('#', 'Premium', 'glyphicon glyphicon-eye-open', 'xcrud-action btn-success', 
        array(  // set action vars to the button
            'data-task' => 'action',
            'data-action' => 'a_basico',
            'data-primary' => '{id_esp}'), 
        array(  // set condition ( when button must be shown)
            'id_niv',
            '=',
            '2'
		)
    );
    $suc->button('#', 'Básico', 'glyphicon glyphicon-eye-close', 'xcrud-action btn-warning', 
		array( // acción
			'data-task' => 'action',
			'data-action' => 'a_premium',
			'data-primary' => '{id_esp}'), 
		array( // condición
			'id_niv',
			'=',
			'1'
		)
	);
		
		
// Imprime la tabla
echo $xcrud->render();
?>
<?php require './public/php/Dashboard_footer.php'; ?>

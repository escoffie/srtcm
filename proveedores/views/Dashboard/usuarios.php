<?php require ('./public/php/Dashboard_header.php'); ?>
<?php
$xcrud = Xcrud::get_instance();
$xcrud->table('proveedores_pro');
$xcrud->table_name('Usuarios');
$xcrud->where('id_rol>',2);
$xcrud->columns('id_rol,avatar_pro',true);
$xcrud->unset_add();
$xcrud->unset_csv();
$xcrud->unset_remove();
$xcrud->label('id_rol', 'Rol');
$xcrud->label('id_niv','Nivel de acceso');
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
$xcrud->change_type('id_niv','checkboxes');
$xcrud->change_type('fecha_pro','datetime');
$xcrud->change_type('pw_pro', 'password', 'md5', array('placeholder'=>'Nueva contraseña'));
$xcrud->validation_pattern('email_pro','email');
$xcrud->relation('id_rol', 'roles_rol', 'id_rol', 'nombre_rol');
$xcrud->relation('id_niv', 'niveles_niv', 'id_niv', 'descripcion_niv');

		
// Imprime la tabla
echo $xcrud->render();
?>
<?php require './public/php/Dashboard_footer.php'; ?>

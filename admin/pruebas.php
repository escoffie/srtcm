<?php
include('../xcrud/xcrud.php');
$theme = 'bootstrap';
//Clientes
$clientes = Xcrud::get_instance();
$clientes->table('clientes_cl');
$clientes->table_name('Clientes', 'Información sobre clientes suscritos para compra en línea', 'icon-users-2');
$clientes->readonly('username_cl', 'edit');
$clientes->fields('fecha_cl', true, false, 'create');
$clientes->pass_var('fecha_cl', date('Y-m-d H:i:s'), 'create');
$clientes->validation_required('username_cl, password_cl, email_cl, nombre_cl, telefono1_cl');
//$clientes->alert_create('email_cl', 'escoffie@think-comunicacion.com', 'Su nueva cuenta', "Apreciable {nombre_cl},/r/nGracias por ser cliente de Pantuflamanía./r/nSu nombre de usuario es {username_cl} y su contraseña es {password_cl} y para acceder entre a Pantuflamanía.com./r/n¡Bienvenido!/r/n/r/nAtentamente,/r/nEduardo Elías Dájer Bechara/r/nDirector general");
$clientes->alert_create('email_cl','','Prueba', 'Hola hola crayola');
$clientes->validation_pattern(array(
	'username_cl'=>'[a-zA-Z0-9]{5,20}',
	'email_cl'=>'email',
	'telefono1_cl' => 'numeric',
	'telefono2_cl' => 'numeric'
));
//$clientes->start_minimized(true);
$clientes_labels = array(
	'username_cl'=>'Usuario',
	'password_cl'=>'Contraseña',
	'email_cl'=>'Correo electrónico',
	'nombre_cl'=>'Nombre completo',
	'telefono1_cl'=>'Teléfono 1',
	'telefono2_cl'=>'Teléfono 2',
	'fecha_cl'=>'Cliente desde',
	'estatus_cl'=>'Estado',
);
$clientes->label($clientes_labels);

//Categorías
$categorias = Xcrud::get_instance();
$categorias->table('categorias_ca');
$categorias->table_name('Categorías', 'Lista de categorías a las que un producto puede pertenecer para facilitar la navegación', 'icon-tags');
$categorias->start_minimized(true);
$categorias->label(array('categoria_ca' => 'Nombre de la categoría'));

//Productos
$productos = Xcrud::get_instance();
$productos->table('productos_pr');
$productos->table_name('Productos', 'Listado de productos disponibles para pedido en línea', 'icon-gift');
$productos->relation('id_ca', 'categorias_ca', 'id_ca', 'categoria_ca', null, null, true);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Clientes</title>
<?php if($theme == 'bootstrap'){ ?>
        <link href="../xcrud/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <?php } ?></head>

<body>
	<div class="container-fluid">
<?php
echo $clientes->render();
echo $categorias->render();
echo $productos->render();
?>
<?php if($theme == 'bootstrap'){ ?>
        <script src="../xcrud/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <?php } ?>
	</div>
</body>
</html>
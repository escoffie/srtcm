<?php require ('./public/php/Dashboard_header.php'); ?>
<?php
$xcrud = Xcrud::get_instance();
$xcrud->table('piepagina_pie');
$xcrud->table_name('Mensaje Pie de Página');
//$xcrud->columns('id_pie,mensaje',true);
//$xcrud->label('id_pie', 'Pie');
//$xcrud->label('mensaje','Mensaje');

// Imprime la tabla
echo $xcrud->render();
?>

<?php require './public/php/Dashboard_footer.php'; ?>
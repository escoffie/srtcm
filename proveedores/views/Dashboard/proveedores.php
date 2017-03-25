<?php require ('./public/php/Dashboard_header.php');

$xcrud = Xcrud::get_instance();
$xcrud->table('proveedores_pro');
$xcrud->table_name('Proveedores');
$xcrud->subselect('ultimo','SELECT timestamp FROM log WHERE codigo_pro={codigo_pro} AND tipo=\'Inicio\' GROUP BY sid ORDER BY timestamp DESC LIMIT 1');
$xcrud->subselect('duracion', 'SELECT TIMEDIFF(
    (SELECT timestamp FROM log WHERE codigo_pro={codigo_pro} ORDER BY timestamp DESC LIMIT 1),
    (SELECT timestamp FROM log WHERE codigo_pro={codigo_pro} AND tipo=\'Inicio\' ORDER BY timestamp DESC LIMIT 1)
) AS Diferencia');
$xcrud->where('id_rol<=',2);
$xcrud->columns('id_rol,avatar_pro',true);
$xcrud->unset_add();
$xcrud->unset_csv();
$xcrud->unset_remove();
$xcrud->label('id_rol', 'Rol');
//$xcrud->label('id_niv','Nivel de acceso');
$xcrud->label('estatus_pro','Estatus');
$xcrud->label('duracion','Duración');
$xcrud->label('fecha_pro','Fecha de alta');
$xcrud->label('codigo_pro','Código interno');
$xcrud->change_type('codigo_pro','int', '000', array('id'=>'prove'));
$xcrud->label('rfc_pro','RFC');
$xcrud->label('razonsocial_pro','Razón social');
$xcrud->label('direccion_pro','Dirección');
$xcrud->label('telefono_pro','Teléfono');
$xcrud->label('email_pro','Email');
$xcrud->label('pw_pro','Contraseña');
$xcrud->label('ultimo','Último acceso');
$xcrud->readonly('codigo_pro,rfc_pro,razonsocial_pro');
$xcrud->fields('id_rol', true);
$xcrud->fields('fecha_pro', true);
$xcrud->fields('avatar_pro', true);
//$xcrud->change_type('id_niv','checkboxes');
$xcrud->change_type('fecha_pro','datetime');
$xcrud->change_type('pw_pro', 'password', 'md5', array('placeholder'=>'Nueva contraseña'));
$xcrud->validation_pattern('email_pro','email');
$xcrud->relation('id_rol', 'roles_rol', 'id_rol', 'nombre_rol');
//$xcrud->change_type('avatar_pro', 'file', '', array('not_rename'=>true));
//$xcrud->relation('id_niv', 'niveles_niv', 'id_niv', 'descripcion_niv');

//print_r($xcrud);
?>
<div class="row">
    <div class="col-md-12" id="monitorFiltroProv"></div>
</div>
<div class="container container-form">
 <img id="img-prov" src="" alt="Imagen Proveedor" height="180%" width="15%" style="display:none"><br>
    <div class="row">
        <div class="col-md-4">
            <form method="post" id="subir_imagen" style="display:none">
                <div class="form-group">
                <input class="form-control" type="file" class="form-control" id = "archivos" name="archivos[]" multiple required>
                </div>
            </form>
            <input type="hidden" id="Edit_Prove" value="">
        </div>
    </div>    
</div>
<?php
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
$suc->unset_print();
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
		
// Bitácora
$log = $xcrud->nested_table('Bitácora','codigo_pro','log','codigo_pro');
$log->where('tipo','Inicio');
$log->order_by('timestamp','desc');
$log->columns('codigo_pro, sid, tipo, accion', true);
$log->fields('codigo_pro, sid, tipo, accion', true);
$log->label(array(
	'timestamp'=>'Fecha y hora de acceso',
));
$log->table_name('Bitácora');
$log->unset_remove();
$log->unset_print();
$log->unset_edit();
$log->unset_add();
$log->unset_csv();

$logDetail = $log->nested_table('Detalles','sid','log','sid');
$logDetail->table_name('Detalles de sesión');
$logDetail->label(array(
	'timestamp'=>'Fecha y hora',
	'tipo' => 'Tipo',
	'accion' => 'Acción'
));
$logDetail->columns('codigo_pro,sid',true);
$logDetail->unset_remove();
$logDetail->unset_view();
$logDetail->unset_edit();
$logDetail->unset_add();
$logDetail->unset_csv();

	
// Imprime la tabla
echo $xcrud->render();
?>
<?php require './public/php/Dashboard_footer.php'; ?>
<script>
$(document).on("xcrudafterrequest",function(event,container){
	
    if(Xcrud.current_task == 'edit')
    {
        //Xcrud.show_message(container,'WOW!','success');
		//alert($('#Edit_Prove').val());
		$('div #subir_imagen').css("display","");
		$('div #img-prov').css("display","");
		DisplayImgProveedor();
    }else{
		$('div #subir_imagen').css("display","none");
		$('div #img-prov').css("display","none");
		}





function DisplayImgProveedor(){
	var data = new FormData();
	
		$.ajax({
					url:'<?php echo URL; ?>Dashboard/traerImgPro/'+document.getElementById("prove").value, 
					type:'POST', 
					contentType:false, 
					data:data, 
					processData:false, 
					cache:false,
					
					}).done(function(msg){
						if(document.getElementById("prove")){
							if(msg != ''){
								$("#img-prov").attr("src","<?php echo URL; ?>/public/images/proveedores/"+msg);
							}else{
								$("#img-prov").attr("src","");
								}
							}else{
									if(msg != ''){
									$("#img_prove").attr("src","<?php echo URL; ?>/public/images/proveedores/"+msg);
									}else{
										$("#img_prove").attr("src","<?php echo URL; ?>/public/images/user.png");
										$('#img_prove').remove();
										//$('#img_prove').removeAttr("height");
										//$('#img_prove').removeAttr("width");
										}
								}
						
					});
	}



	

});




</script>
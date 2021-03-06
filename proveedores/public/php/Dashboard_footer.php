        	<footer style="margin-top:20px;">
            	<div class="well">
                    <p>Derechos reservados, &copy; 2015 - <?php echo date('Y'); ?>, Surticoma</p>
                    <p><strong>¿Necesita ayuda?</strong> <i class="fa fa-phone"></i> <span id="mensajePie"></span>
                    <!--(999) 611 8100 ext. 241 de lunes a viernes, de 9:00 a 17:00 y sábados de 9:00 a 14:00.--></p>
                    <!-- Eddier Bacab 911 8100 x 241 9:00 a 17:00 l a v y 9:00 a 14:00 s -->
                </div>
            </footer>
        
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="<?php echo BOWER; ?>bower_components/jquery/dist/jquery.min.js"></script>
    
    <!--plugin retry ajax -->
    <script src="<?php echo URL; ?>public/js/jquery.ajax-retry.min.js"></script> 
    
    <!-- jQuery UI
    <script src="<?php echo URL; ?>../xcrud/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?php echo URL; ?>../xcrud/plugins/timepicker/jquery-ui-timepicker-addon.js"></script> -->
    <!--<script src="<?php echo URL; ?>../xcrud/plugins/xcrud.js"></script>-->

    <!-- Bootbox JavaScript -->
    <script src="<?php echo URL; ?>public/js/bootbox.min.js"></script>
    
    <!-- jQuery Match Height JavaScript -->
    <script src="<?php echo URL; ?>public/js/jquery.matchHeight.js"></script>
    
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo BOWER; ?>bower_components/metisMenu/dist/metisMenu.min.js"></script>
    
    <!-- 20170314 asunza Multi-Select Plugin JavaScript -->
    <script src="<?php echo BOWER; ?>bower_components/multiSelect/dist/js/bootstrap-select.min.js"></script>
   
    <!-- Moments Javascript -->
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    
    <!-- DataTables JavaScript 
    <script src="<?php echo BOWER; ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo BOWER; ?>bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>-->
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.12/fc-3.2.2/sc-1.4.2/datatables.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.12/sorting/datetime-moment.js"></script>
    
    <!-- Date Range Picker Javascript -->
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>

    <!-- Morris Charts JavaScript -->
    
	<script src="<?php echo BOWER; ?>bower_components/raphael/raphael-min.js"></script>
    <script src="<?php echo BOWER; ?>bower_components/morrisjs/morris.min.js"></script>

    <!-- jQuery LoadingOverlay -->
    <script src="<?php echo URL; ?>public/js/overlay/src/loadingoverlay.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo BOWER; ?>dist/js/sb-admin-2.js"></script>

    <!-- Acciones personalizadas JavaScript -->
    <script>
    var constantes = {
		'URL': '<?php echo URL; ?>',
		'codigo_pro': <?php echo $_SESSION['usuario']['codigo_pro']; ?>,
	}
	var fechas = {
		'desde': '<?php echo $_SESSION['filtro']['fechaA_desde']; ?>',
		'hasta': '<?php echo $_SESSION['filtro']['fechaA_hasta']; ?>',
		'menosunasemana': '<?php echo date('Y-m-d', strtotime($_SESSION['filtro']['fechaA_hasta'].' -1 week + 1 day')); ?>',
		'menosunmes': '<?php echo date('Y-m-d', strtotime($_SESSION['filtro']['fechaA_hasta'].' -1 month +1 day')); ?>',
		'primerodemes': '<?php echo date('Y-m-01', strtotime($_SESSION['filtro']['fechaA_hasta'])); ?>',
		'mespasadodesde': '<?php echo date('Y-m-01', strtotime($_SESSION['filtro']['fechaA_hasta'].' -1 month')); ?>',
		'mespasadohasta': '<?php echo date('Y-m-t', strtotime($_SESSION['filtro']['fechaA_hasta'].' -1 month')); ?>',
	}
	


	
//funcion para subir la imagen 
$(document).on("change","#archivos",function(){
	var archivos = document.getElementById("archivos");//Damos el valor del input tipo file
	var archivo = archivos.files; //Obtenemos el valor del input (los arcchivos) en modo de arreglo
	texto = null;
	//El objeto FormData nos permite crear un formulario pasandole clave/valor para poder enviarlo
	
	if(document.getElementById("prove")){
		texto = document.getElementById("prove").value;
		}
		
	if(texto != null){
	 
			var data = new FormData();
			//Como no sabemos cuantos archivos subira el usuario, iteramos la variable y al 
			//objeto de FormData con el metodo "append" le pasamos calve/valor, usamos el indice "i" para
			//que no se repita, si no lo usamos solo tendra el valor de la ultima iteracion
			
			if(archivo.length > 0){
					for(i=0; i<archivo.length; i++){
						data.append('archivo'+i,archivo[i]);	
					}
					
					data.append('texto',texto);
					
					$.ajax({
						url:constantes.URL+"Dashboard/cambiaImgaProv", //Url a donde la enviaremos
						type:'POST', //Metodo que usaremos
						contentType:false, //Debe estar en false para que pase el objeto sin procesar
						data:data, //Le pasamos el objeto que creamos con los archivos
						processData:false, //Debe estar en false para que JQuery no procese los datos a enviar
						cache:false //Para que el formulario no guarde cache
					}).done(function(msg){
						//$("#cargados").append(msg); //Mostrara los archivos cargados en el div con el id "Cargados"				
						x = msg.split("-");
						if(x[1] == 1){
							//alert('todo bien');
							$("#monitorFiltroProv").html('').append('<div class="alert alert-success"><strong>'+x[0]+'</strong></div>');
					
							$("#img-prov").attr("src",constantes.URL+"/public/images/proveedores/"+x[2]);
							}else{
								//alert('error');
								$("#monitorFiltroProv").html('').append('<div class="alert alert-danger"><strong>'+x[0]+'</strong></div>');
								}	
					});
			}else{
						$("#monitorFiltroProv").html('').append('<div class="alert alert-danger"><strong>Por favor seleccione una iamgen.</strong></div>');
				}
	}else{
		$("#monitorFiltroProv").html('').append('<div class="alert alert-danger"><strong>Para Cambiar la imagen elija un proveedor para editar</strong></div>');
		}
});


var data = new FormData();
		//data.append('correo',$("#usuario").val());
		//alert('funcion traer pie de pagina');
		$.ajax({
				url:constantes.URL+'Dashboard/piepagina', 
				type:'POST', 
				contentType:false, 
				data:data, 
				processData:false, 
				cache:false,
				
				}).done(function(msg){
					$("#mensajePie").html(msg);
			});


	
    </script>
    
    <?php echo Xcrud::load_js() ?>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo BOWER; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <!-- Bootstrap Tour -->
    <script src="<?php echo URL; ?>public/js/bootstrap-tour/js/bootstrap-tour.min.js"></script>
    
    <script>
	var bootstrapButton = $.fn.button.noConflict(); // return $.fn.button to previously assigned value
	$.fn.bootstrapBtn = bootstrapButton;
	</script>
    
    <?php if(!isset($acciones)){ ?>
    <script src="<?php echo URL; ?>public/js/dashboard-acciones.js"></script>
    <script src="<?php echo URL; ?>public/js/dashboard-acciones-tour.js"></script>
	<?php } ?>
</body>

</html>

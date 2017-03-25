// JavaScript que se ejecuta al cargarse el DOM

$(document).ready(function(){
	"use strict";
	
	// Sincronización manual
	if(constantes.codigo_pro==0){
		
		$('.importar-datos').click(function(e){
			e.preventDefault();
			$.ajax({
				url:$(this).attr('href'),
				success:function(respuesta){
					bootbox.alert({message:respuesta, title:'Reporte de importación de archivo', size:'large' });
				},
			});
			//bootbox.alert( { message:$(this).attr('href') } );
		});
		
		//$('#listarArchivosRemotos').DataTable();
	}
	
	// Timer
	if(constantes.codigo_pro!=0){
		var idleState = false;
		var idleTimer = null;
		var howMuch = 5*60; //En segundos
		$('*').bind('click mouseup mousedown keydown keypress keyup submit change scroll resize dblclick', function () {
			clearTimeout(idleTimer);
			if (idleState == true) {
				//Cuando está ocupado
			}
			idleState = false;
			idleTimer = setTimeout(function () {
				// Cuando está ocioso
				var cuenta = 60;
				var intervalo = setInterval(function(){
					cuenta--;
					if(cuenta<0){
						clearInterval(intervalo);
						if(idleState===true){
							bitacora('Cierre', 'Cierre de sesión por inactividad');
							unload();
						}
					} else {
						$('#conteo').text(cuenta);
					}
				},1000);
				bootbox.alert("Su sesión terminará automáticamente en <strong><span id=\"conteo\"></span></strong> segundos. Si desea continuar trabajando, haga clic en el botón OK para descartar este aviso.");
				$.playSound("http://www.noiseaddicts.com/samples_1w72b820/3724");
				idleState = true; 
			}, howMuch*1000);
		});
		$("body").trigger("click");
	}

	// Al cerrar sesión
	var unloaded = false;
	//$(window).on('beforeunload', unload);
	//$(window).on('unload', unload);  
	function unload(){      
		if(!unloaded){
			$('body').css('cursor','wait');
			console.log('Ejecutando cierre');
			$.ajax({
				type: 'get',
				async: false,
				url: constantes.URL+'User/destroySession',
				success:function(){ 
					unloaded = true; 
					$('body').css('cursor','default');
					window.location.replace(constantes.URL);
				},
				timeout: 5000
			}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
		}
	}
	
	// Cerrar sesión
	$('#cerrar-sesion').click(function(e){
		//e.preventDefault();
		
		bitacora('Cierre', 'Cierre de sesión manual');

		$(window).on("beforeunload", unload);
		$(window).on("unload", unload);
	});
	
	// Asigna el valor de "id_niv" en el filtro con base al valor más bajo de las sucursales seleccionadas en los checkboxes
	
	var asignarNivel = function(){
		
		var id_nivs=[];
		var _nivel;
		var nivel;
		var prependLabel;
		
		if($('input.check_sucursales:checked').length>0) {
		
			$('input.check_sucursales:checked').each(function(index, element) {
				_nivel = $(element).data('nivel');
				if(nivel!==""){
					id_nivs.push( _nivel );
				}
				nivel = Math.min.apply(Math,id_nivs);
				if(nivel===Infinity){
					nivel=1;	
				}
				$('#id_niv').val(nivel);
				
				if(nivel===1){
					prependLabel = '<span class="label label-warning" id="prepend-label">Básico</span>';
				} else if (nivel===2){
					prependLabel = '<span class="label label-success" id="prepend-label">Premium</span>';	
				}
				$('#prepend-label').remove();
				$('#nombre_de_usuario').prepend(prependLabel);
			});
		
		} else {
			$('#prepend-label').remove();
		}
		
	};
	
	asignarNivel();
	
	$('.check_sucursales').change(asignarNivel);

	
	// Javascript to enable link to tab
	var url = document.location.toString();
	var hashmodificado = '';
	if (url.match('#')) {
		$('.nav-tabs a[href="#' + url.split('#')[1].replace('_btn','') + '"]').tab('show');
	} 
	
	// Change hash for page-reload
	$('.nav-tabs a').on('shown.bs.tab', function (e) {
		window.location.hash = e.target.hash+'_btn';
		bitacora('Navegación', $(this).attr('href').replace('#',''));
		//console.log($(this).attr('href'));
	});
		
	// Paneles a la misma altura
	$('.equal-height-panels .panel').matchHeight();
	
	// Subfamilias del filtro (selects dependientes) al cargar y al cambiar el select de familias
	obtenerSubfamilias($('#codigo_fam').val());	
	
	$('#codigo_fam').on("click change", this, function(){
		obtenerSubfamilias($(this).val());	
	});
	
	// Botón del filtro
	$('#gui_btn_filtro').click(function(e){
		e.preventDefault();
		
		bitacora('Filtro','Filtrar');
		
		$("#monitorFiltro").html('').append('<div class="alert alert-warning"><strong>Cargando.</strong> Por favor, espere...</div>');
		
		//Validación
		var error_filtro = '';
		if($(".check_sucursales:checkbox:checked").length == 0){
			error_filtro += '<p>Seleccione al menos una sucursal.</p>';
		}
		if($("#codigo_fam").val()==='0'){
			error_filtro += '<p>Seleccione una familia.</p>';
		}
		if(error_filtro!==''){
			bootbox.dialog({
				'title':'Verifique los siguientes errores', 
				'message':error_filtro,
				buttons:{
					'danger': {
						label: 'Ok',
						className:'btn-danger'
					},
				}
			});
		} else {
			//AJAX si no hay errores
			var _post = $("#gui_form_filtro").serialize();
			$.ajax({
				type:'post',
				url: constantes.URL + 'Dashboard/filtro',
				data: _post,
				success: function(){
					$("#market-share").LoadingOverlay("show");
					$("#venta-por-dia").LoadingOverlay("show");
					$("#comparativo").LoadingOverlay("show");
					totalesPorPeriodo();
					histograma();
					sesionMonitor();
					comparativoDropdown();
					monitorFiltro();
					$("#gui_btn_exportar").show();
					
					// Relacionado con el TOUR
					$('a[aria-controls="market-share"]').tab('show');
					tour.next();

				},
			}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
		}
	});
	
	$.get(constantes.URL+'Dashboard/sesionFiltro', function(sessionData){
		if(sessionData==1){
			$("#monitorFiltro").html('').append('<div class="alert alert-warning"><strong>Cargando.</strong> Por favor, espere...</div>');
			$("#market-share").LoadingOverlay("show");
			$("#venta-por-dia").LoadingOverlay("show");
			$("#comparativo").LoadingOverlay("show");
			totalesPorPeriodo();
			histograma();
			sesionMonitor();
			comparativoDropdown();
			monitorFiltro();
			$("#gui_btn_exportar").show();
		}
	}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});

	// DATATABLES defaults en general
	$.fn.dataTable.moment( 'DD/MM/YYYY' );
	$.extend( $.fn.dataTable.defaults,{
			"responsive": true,
			"scrollX":true,
			"language": {
				"sProcessing": "Procesando...",
				"sLengthMenu": "Mostrar _MENU_ registros",
				"sZeroRecords": "No se encontraron resultados",
				"sEmptyTable": "Ningún dato disponible en esta tabla",
				"sInfo": "Mostrando  del _START_ al _END_ de un total de _TOTAL_ ",
				"sInfoEmpty": "No hay registros para mostrar",
				"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
				"sInfoPostFix": "",
				"sSearch": "Buscar:",
				"sUrl": "",
				"sInfoThousands": ",",
				"sLoadingRecords": "Cargando...",
				"oPaginate": {
					"sFirst": "Primero",
					"sLast": "Último",
					"sNext": "Siguiente",
					"sPrevious": "Anterior"
				},
				"oAria": {
					"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
					"sSortDescending": ": Activar para ordenar la columna de manera descendente"
				}
			},
	});
	
	// MORRIS.JS
	
	$("#morris-1, #morris-2, #morris-comparacion-1, #morris-comparacion-2").on('mouseover resize',function() {
        preparaDonaMorris();
    });
    preparaDonaMorris();
	
	
	// Periodo actual
	var M2 = Morris.Donut({
		element:'morris-2',
		data:[{label:'Aplique un filtro para ver los datos',value:0}],
		formatter: function (y, datos) { return y + '%'; },
		resize:true,
		colors:colores,
	});
	
	// Periodo anterior
	var M1 = Morris.Donut({
		element:'morris-1',
		data:[{label:'Aplique un filtro para ver los datos',value:0}],
		formatter: function (y, datos) { return y + '%' },
		resize:true,
		colors:colores,
	});
	
	// Morris line graph
	var opcionesLineCharts1 = {
		element:'morris-3',
		data:[{'xkey':fechas.hasta, '1':0}],
		xkey: 'xkey',
		ykeys: ['1'],
		labels: ['No hay datos'],
		yLabelFormat:function (y) { return '$ ' + y.toString(); },
		resize:true,
		lineColors: colores,
	}
	
	var opcionesLineCharts2 = {
		element:'morris-4',
		data:[{'xkey':fechas.hasta, '1':0}],
		xkey: 'xkey',
		ykeys: ['1'],
		labels: ['No hay datos'],
		yLabelFormat:function (y) { return '$ ' + y.toString(); },
		resize:true,
		lineColors:colores,
	}
	
	window.M3 = Morris.Line(opcionesLineCharts1);
	window.M4 = Morris.Line(opcionesLineCharts2);
	
	// Morris barras
	var opcionesMorrisBarras = {
		element: 'morris-barras',
		data: [{'proveedor':'No hay datos', 'suma_anterior':0, 'suma_actual':0}],
		xkey: 'proveedor',
		ykeys: ['suma_anterior', 'suma_actual'],
		labels: ['Anterior', 'Actual'],
		barColors:[colores[1], colores[0]],
	}
	
	window.MorrisBarras = Morris.Bar(opcionesMorrisBarras);
	
	// Morris comparativo (tercera pestaña)
	var opcionesMorrisComparativo1 = {
		element:'morris-comparacion-1',
		data:[{label:'Seleccione un competidor para ver los datos',value:0}],
		formatter: function (y, datos) { return y + ' u' },
		resize:true,
		colors:colores,
	}
	var opcionesMorrisComparativo2 = {
		element:'morris-comparacion-2',
		data:[{label:'Seleccione un competidor para ver los datos',value:0}],
		formatter: function (y, datos) { return y + ' u' },
		resize:true,
		colors:colores,
	}
	
	window.MorrisComparativo1 = Morris.Donut(opcionesMorrisComparativo1);
	window.MorrisComparativo2 = Morris.Donut(opcionesMorrisComparativo2);
	
	function totalesPorPeriodo(){
		
		// General
		var filtro_sucursales_array = [];
		$('[name="filtro_sucursales[]"]:checked').each(function(){
			filtro_sucursales_array.push($(this).val());
		});
		
		var ms1 = $("#marketshare1").DataTable({
					retrieve:true,
					order: [[ 0, "asc" ]],
					columns: [
						{ title: "Detalles" },
						{ title: "Proveedor" },
						{ title: "Venta" },
						{ title: "Market Share" },
					],
					dom: '<"wrapper"it>'
					
				});
		var ms2 = $("#marketshare2").DataTable({
					retrieve:true,
					order: [[ 0, "asc" ]],
					columns: [
						{ title: "Detalles" },
						{ title: "Proveedor" },
						{ title: "Venta" },
						{ title: "Market Share" },
					],
					dom: '<"wrapper"it>'

				});
		
		//Obtiene datos por AJAX para Morris
		$.ajax({
			method: "POST",
			url: constantes.URL+"Dashboard/totalesPorPeriodo",
			data: {
				filtro_sucursales:filtro_sucursales_array,
				codigo_fam: $('#codigo_fam').val(),
				codigo_sub: $('#codigo_sub').val(),
				rangodefechas:$('#rangodefechas').val(),
				periodo:$('[name="periodo"]:checked').val(),
				cual_periodo:"actual",
			},
			success:function(datos){
				//Periodo actual
				$("#morris-2-leyenda ul").html('');
				preparaDonaMorris();
				M2.setData(datos.actual.morris);
				$(datos.actual.morris).each(function(index, item){
					var color = M2.options.colors[index];
					$("#morris-2-leyenda ul").append(function(){
						return $(document.createElement('li')).addClass('ellipsis').html('<span class="label label-default" style="display:inline-block; width:45px; text-align:right; margin-right:5px; background-color:'+color+'"> '+item.value+'%</span>'+item.label).on('click mouseover', function(){M2.select(index); preparaDonaMorris(); });
					});
				});
				var m2fini = moment(datos.actual.fechas.desde).format('DD/MM/YYYY');
				var m2fend = moment(datos.actual.fechas.hasta).format('DD/MM/YYYY');
				$("#morris-2-fechas, #morris-4-fechas").html('<h3>Desde '+m2fini+' hasta '+m2fend+'</h3>');
				ms2.clear().rows.add(datos.actual.tabla).columns.adjust().draw();
				$("#marketshare2 .fa-square").each(function(index){
					$(this).css("color", colores[index]);	
				});
				
				$('#marketshare2 tbody button').click( function () {
					var arreglo = $( this );
					//console.log( arreglo.data('codigopro'), arreglo.data('fechaini'), arreglo.data('fechafin') );
					$.ajax({
						type:'post',
						data: {
							codigo_pro: arreglo.data('codigopro'),
							fecha_ini : arreglo.data('fechaini'),
							fecha_fin : arreglo.data('fechafin'),
						},
						url: constantes.URL	+ 'Dashboard/ventaPorDia',
						success: function(data){
								
							bitacora('Interacción', 'Abrir Venta por día periodo actual');
								
							bootbox.dialog( {
								size: 'large',
								title: arreglo.data('nombrepro'),
								message: data,
								buttons: {
									success: {
										label: "Cerrar",
										className: "btn-success",
									},
								}
							});
							// Detalles tickets en Market Share
							$('.verDetalleTickets').click(function(){
								
								bitacora('Interacción', 'Detalle de tickets periodo actual');
								
								var id 			= $(this).data('id');
								var codigo_art 	= $(this).data('codigoart');
								var fecha 		= $(this).data('fecha');
								var sucursales 	= $(this).data('sucursales');
								var yacargado	= false;
								//$('#toggle-'+id).toggleClass('hidden');
								//$('#toggle-'+id).toggleClass('show');
								if(yacargado===false){
									$.ajax({
										type:'post',
										data:{
											id: id,
											codigo_art: codigo_art,
											fecha: fecha,
											sucursales: sucursales,
										},
										url: constantes.URL + 'Dashboard/ticketsPorArticuloPorDia',
										success: function(data){
											$('#tickets-'+id).empty();
											$('#tickets-'+id).html(data);
											yacargado = true;
										}
									}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
								}
							});

						},
					}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
				} );

				//Periodo anterior
				$("#morris-1-leyenda ul").html('');
				M1.setData(datos.anterior.morris);
				preparaDonaMorris();
				$(datos.anterior.morris).each(function(index, item){
					var color = M1.options.colors[index];
					$("#morris-1-leyenda ul").append(function(){
						return $(document.createElement('li')).addClass('ellipsis').html('<span class="label label-default" style="display:inline-block; width:45px; text-align:right; margin-right:5px; background-color:'+color+'"> '+item.value+'%</span>'+item.label).on('click mouseover', function(){M1.select(index); preparaDonaMorris(); });
					});
				});
				var m1fini = moment(datos.anterior.fechas.desde).format('DD/MM/YYYY');
				var m1fend = moment(datos.anterior.fechas.hasta).format('DD/MM/YYYY');
				$("#morris-1-fechas, #morris-3-fechas").html('<h3>Desde '+m1fini+' hasta '+m1fend+'</h3>');
				ms1.clear().rows.add(datos.anterior.tabla).columns.adjust().draw();
				$("#marketshare1 .fa-square").each(function(index){
					$(this).css("color", colores[index]);	
				});
				
				$('#marketshare1 tbody button').click( function () {
					var arreglo = $( this );
					//console.log( arreglo.data('codigopro'), arreglo.data('fechaini'), arreglo.data('fechafin') );
					$.ajax({
						type:'post',
						data: {
							codigo_pro: arreglo.data('codigopro'),
							fecha_ini : arreglo.data('fechaini'),
							fecha_fin : arreglo.data('fechafin'),
						},
						url: constantes.URL	+ 'Dashboard/ventaPorDia',
						success: function(data){
								
							bitacora('Interacción', 'Abrir Venta por día periodo anterior');
								
							bootbox.dialog( {
								size: 'large',
								title: arreglo.data('nombrepro'),
								message: data,
								buttons: {
									success: {
										label: "Cerrar",
										className: "btn-success",
									},
								}
							});
							// Detalles tickets en Market Share
							$('.verDetalleTickets').click(function(){
								
								bitacora('Interacción', 'Detalle de tickets periodo anterior');
								
								var id 			= $(this).data('id');
								var codigo_art 	= $(this).data('codigoart');
								var fecha 		= $(this).data('fecha');
								var sucursales 	= $(this).data('sucursales');
								var yacargado	= false;
								//$('#toggle-'+id).toggleClass('hidden');
								//$('#toggle-'+id).toggleClass('show');
								if(yacargado===false){
									$.ajax({
										type:'post',
										data:{
											id: id,
											codigo_art: codigo_art,
											fecha: fecha,
											sucursales: sucursales,
										},
										url: constantes.URL + 'Dashboard/ticketsPorArticuloPorDia',
										success: function(data){
											$('#tickets-'+id).empty();
											$('#tickets-'+id).html(data);
											yacargado = true;
											
											
										}
									}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
								}
							});

						},
					}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
				} );
				
				//jQuery Match Height después de actualizar contenido
				$.fn.matchHeight._update();
								
			},
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});

	}
	
	function histograma(){
		
		// Gráficas: Regresa ambos periodos
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/histogramaMorrisData',
			success:function(datos){
				if(1 in datos){
					window.M3.options.labels = datos[1].labels;
					window.M3.options.ykeys = datos[1].ykeys;
					window.M3.setData(datos[1].data);
				}
				if(2 in datos){
					window.M4.options.labels = datos[2].labels;
					window.M4.options.ykeys = datos[2].ykeys;
					window.M4.setData(datos[2].data);
				}
			},
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
		
		// Tablas: Regresa ambos periodos
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/histograma',
			success:function(data){
				
				$("#histograma").html(data);
				$("#hist-1").DataTable({fixedColumns: {leftColumns: 2},"order": [[ 0, "desc" ]]});
				$("#hist-2").DataTable({fixedColumns: {leftColumns: 2},"order": [[ 0, "desc" ]]});
				$.ajax({
					type:'post',
					url: constantes.URL + 'Dashboard/compararTotalesPorPeriodo',
					success:function(datos){
						
						window.MorrisBarras.setData(datos);
						
						$("#morris-barras-leyenda").html('');
						window.MorrisBarras.options.labels.forEach(function(label, i){
							var legendItem = $('<span></span>').text(label).css('color', window.MorrisBarras.options.barColors[i]);
							$("#morris-barras-leyenda").append(legendItem);
						});
						
				
						$.ajax({
							type:'post',
							url: constantes.URL + 'Dashboard/compararTotalesPorPeriodoTabla',
							success:function(datos){
								$("#morris-barras-tabla").html('').html(datos);
								$("#morris-barras-tabledata").DataTable();
							},
						}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
				
						
					},
				}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
			},
			
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
		
		

	}
	
	// Comparativo: genera el Morris y el listado de productos
	$("#comparativoDropdown1").on('change', function(){
		
		bitacora('Interacción','Cambio comparativo izquierdo');
		
		window.MorrisComparativo1.setData([{label:'Cargando...',value:0}]);
		$("#morris-comparacion-1-leyenda ul").html('').append('<li><img src="'+constantes.URL+'public/images/spinner.gif"></li>');
		$.ajax({
			type:'post',
			data:{
				codigo_pro:	$(this).val(),
			},
			url:constantes.URL+'Dashboard/comparativoDataMorris',
			success:function(datos){
				window.MorrisComparativo1.setData(datos);
				preparaDonaMorris();
				$("#morris-comparacion-1-leyenda ul").html('');
				$(datos).each(function(index, item){
					var color = window.MorrisComparativo1.options.colors[index];
					$("#morris-comparacion-1-leyenda ul").append(function(){
						return $(document.createElement('li')).addClass('ellipsis').html('<span class="label label-default" style="display:inline-block; width:45px; text-align:right; margin-right:5px; background-color:'+color+'"> '+item.value+'</span></span>'+'<span title="'+item.label+'" data-toggle="tooltip" data-placement="left" >'+item.label+'</span>').on('click mouseover', function(){window.MorrisComparativo1.select(index); preparaDonaMorris(); });
					});
				});
			}
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
		var comparativoTabla1 = $("#comparativo-table-1").DataTable({
			retrieve:true,
			columns:[
				{title:'Fecha'},
				{title:'Código de barras'},
				{title:'Artículo'},
				{title:'Presentación'},
				{title:'Unidades'},
				{title:'Monto'},
			],
		});

		$.ajax({
			type:'post',
			data:{
				codigo_pro:	$(this).val(),
			},
			url:constantes.URL+'Dashboard/comparativoDataTable',
			success:function(datos){
				//console.log(datos);
				comparativoTabla1.clear().rows.add(datos).draw();
			}
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
	});

	$("#comparativoDropdown2").on('change load', function(){
		
		bitacora('Interacción','Cambio comparativo derecho');
		
		window.MorrisComparativo2.setData([{label:'Cargando...',value:0}]);
		$("#morris-comparacion-2-leyenda ul").html('').append('<li><img src="'+constantes.URL+'public/images/spinner.gif"></li>');
		$.ajax({
			type:'post',
			data:{
				codigo_pro:	$(this).val(),
			},
			url:constantes.URL+'Dashboard/comparativoDataMorris',
			success:function(datos){
				window.MorrisComparativo2.setData(datos);
				preparaDonaMorris();
				$("#morris-comparacion-2-leyenda ul").html('');
				$(datos).each(function(index, item){
					var color = window.MorrisComparativo2.options.colors[index];
					$("#morris-comparacion-2-leyenda ul").append(function(){
						return $(document.createElement('li')).addClass('ellipsis').html('<span class="label label-default" style="display:inline-block; width:45px; text-align:right; margin-right:5px; background-color:'+color+'"> '+item.value+'</span>'+'<span title="'+item.label+'" data-toggle="tooltip" data-placement="left" >'+item.label+'</span>').on('click mouseover', function(){window.MorrisComparativo2.select(index); preparaDonaMorris(); });
					});
				});
				
			}
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
		var comparativoTabla2 = $("#comparativo-table-2").DataTable({
			retrieve:true,
			columns:[
				{title:'Fecha'},
				{title:'Código de barras'},
				{title:'Artículo'},
				{title:'Presentación'},
				{title:'Unidades'},
				{title:'Monto'},
			],
		});

		$.ajax({
			type:'post',
			data:{
				codigo_pro:	$(this).val(),
			},
			url:constantes.URL+'Dashboard/comparativoDataTable',
			success:function(datos){
				comparativoTabla2.clear().rows.add(datos).draw();
			}
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
		
	});

	// Comparativo: genera el dropdown
	function comparativoDropdown(){
		
		window.MorrisComparativo1.setData([{label:'Seleccione un competidor del menú desplegable',value:0}]);
		$("#morris-comparacion-1-leyenda ul").html('').append('<li>Seleccione un competidor</li>');
		
		window.MorrisComparativo2.setData([{label:'Seleccione un competidor del menú desplegable',value:0}]);
		$("#morris-comparacion-2-leyenda ul").html('').append('<li>Seleccione un competidor</li>');
		
		$(".comparativoDropdown select").html('').append('<option>&#x2248; Cargando. Espere...</option>').prop('disabled','disabled');
		
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/comparativoDropdown',
			success:function(datos){
				if(datos!=0){
					$(".comparativoDropdown select").html('').append(datos).prop('disabled',false);
					if(constantes.codigo_pro!=0){
						$("#comparativoDropdown1").trigger("change");
					}
				} else {
					$(".comparativoDropdown select").html('').append('<option value="0">No hay proveedores que coincidan con este filtro</option>').prop('disabled','disabled');
				}
				//<option value="0">No hay proveedores que coincidan con este filtro</option>
				//$("#comparativoDropdown2").html('').append(datos).prop('disabled',false);
				//$("#comparativoDropdown1 select").trigger("change");
			},
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]}).then(function(){console.log('comparativoDropdown cargado')});
		
		
	}
	
	/* ACTUALIZA DATOS USUARIO */
	$("#perfilUpdate_form").on('submit', function(event){
		event.preventDefault();
		$.ajax({
			type:'post',
			url: constantes.URL+'Dashboard/perfilUpdate',
			data:$(this).serialize(),
			success:function(data){
												
				bitacora('Interacción', 'Actualización de datos');

				console.log('perfilUpdate',data);
				bootbox.alert("Mensaje: "+data.mensaje);
				$('#alert-perfil').alert('close');
			},
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
	});
	
	function sesionMonitor(){
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/sesionMonitor',
			success:function(data){
				$('#sesion-monitor').html(data);
			},
		}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});	
	}
	
	
	// Activa tooltips
	//$('[data-toggle="tooltip"]').tooltip();
	$('body').tooltip({
			selector:'[data-toggle="tooltip"]',
			container:'body',
		});
			
});
// Termina on load
//===============================

// Acciones del filtro

function obtenerSubfamilias(val) {
	"use strict";
	$.ajax({
		method: "POST",
		url: constantes.URL+"Dashboard/subfamiliasPorFamiliaPorProveedor/"+val,
		success: function(data){
			$("#codigo_sub").html(data);
		}
	}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});
	
}

// Monitor Filtro
function monitorFiltro(){
	$.ajax({
		type:'post',
		url:constantes.URL+'Dashboard/monitorFiltro',
		success:function(datos){
			$("#monitorFiltro").html('').append(datos);
			$("#market-share").LoadingOverlay("hide");
			$("#venta-por-dia").LoadingOverlay("hide");
			$("#comparativo").LoadingOverlay("hide");
		},
	}).retry({times:3, timeout:1000, statusCodes:[500,503,504]}).then(function(){console.log('monitorFiltro cargado')});
}

// Morris Donas

var colores = ['#3A6603','#7F0000', '#FF4C4C', '#FF0000', '#7F2626', '#CC0000','#7F0000', '#FF4C4C', '#FF0000', '#7F2626', '#CC0000','#7F0000', '#FF4C4C', '#FF0000', '#7F2626', '#CC0000'];

$("#morris-1").mouseover(function(e){
	preparaDonaMorris();
	//bitacora('Interacción', 'Interacción con gráfico de pie 1');
});
$("#morris-2").mouseover(function(e){
	preparaDonaMorris();
	//bitacora('Interacción', 'Interacción con gráfico de pie 2');
});

function preparaDonaMorris(){
	
	$("#morris-1 tspan:first").css("display","none");
	$("#morris-1 tspan:nth-child(1)").css({"font-size":"30px", "margin-top":"-20px"});

	var isi = $("#morris-1 tspan:first").html();
	$('#morris-1-etiquetas').text(isi);

	$("#morris-2 tspan:first").css("display","none");
	$("#morris-2 tspan:nth-child(1)").css({"font-size":"30px", "margin-top":"-20px"});

	var isi = $("#morris-2 tspan:first").html();
	$('#morris-2-etiquetas').text(isi);
	
	$("#morris-comparacion-1 tspan:first").css("display","none");
	$("#morris-comparacion-1 tspan:nth-child(1)").css({"font-size":"30px", "margin-top":"-20px"});

	var isi = $("#morris-comparacion-1 tspan:first").html();
	$('#morris-comparacion-1-etiquetas').text(isi);
	
	$("#morris-comparacion-2 tspan:first").css("display","none");
	$("#morris-comparacion-2 tspan:nth-child(1)").css({"font-size":"30px", "margin-top":"-20px"});

	var isi = $("#morris-comparacion-2 tspan:first").html();
	$('#morris-comparacion-2-etiquetas').text(isi);
	
}

// Date Range Picker
// Referencia: http://www.daterangepicker.com/

$('#rangodefechas').daterangepicker({
    /*"dateLimit": {
        "days": 7
    },*/
    "ranges": {
        "últimos 7 días": [
            fechas.menosunasemana,
            fechas.hasta
        ],
        "Últimos 30 días": [
            fechas.menosunmes,
            fechas.hasta
        ],
        "Este mes": [
            fechas.primerodemes,
            fechas.hasta
        ],
        "Mes pasado": [
            fechas.mespasadodesde,
            fechas.mespasadohasta
        ]
    },
	"alwaysShowCalendars": true,
    "locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "Desde",
        "toLabel": "Hasta",
        "customRangeLabel": "Personalizado",
        "weekLabel": "W",
        "daysOfWeek": [
            "Do",
            "Lu",
            "Ma",
            "Mi",
            "Ju",
            "Vi",
            "Sá"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
        "firstDay": 1
    },
    "alwaysShowCalendars": true,
    "startDate": fechas.desde,
    "endDate": fechas.hasta
}, function(start, end, label) {
  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
});

//jQuery LoadingOverlay
$.LoadingOverlaySetup({
	image			:	constantes.URL+'public/images/spinner.gif',
	resizeInterval	:	20,
	zIndex			:	1001,
	imagePosition	:	'center 100px',
});

// Log de acciones de usuario

var bitacora = function(tipo, accion){
	'use strict';
	$.ajax({
		'url': constantes.URL+'Dashboard/log/'+tipo+','+accion,
		'method': 'get',
		success: function(resultado){
			console.log('Log', tipo, accion, 'Resultado ',resultado);
		},
	}).retry({times:3, timeout:1000, statusCodes:[500,503,504]});	
};

// Play sound
// https://github.com/admsev/jquery-play-sound/blob/master/jquery.playSound.js
(function($){

  $.extend({
    playSound: function(){
      return $(
        '<audio autoplay="autoplay" style="display:none;">'
          + '<source src="' + arguments[0] + '.mp3" />'
          + '<source src="' + arguments[0] + '.ogg" />'
          + '<embed src="' + arguments[0] + '.mp3" hidden="true" autostart="true" loop="false" class="playSound" />'
        + '</audio>'
      ).appendTo('body');
    }
  });

})(jQuery);

// Prueba
function prueba(){
	alert('Foo');
}
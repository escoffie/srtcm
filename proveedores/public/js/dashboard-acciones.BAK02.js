// JavaScript que se ejecuta al cargarse el DOM

$(document).ready(function(){
	"use strict";
	
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
		var _post = $("#gui_form_filtro").serialize();
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/filtro',
			data: _post,
			success: function(){
				totalesPorPeriodo();
				histograma();
				sesionMonitor();
			},
		});
	});

	// DATATABLES defaults en general
	$.fn.dataTable.moment( 'DD/MM/YYYY' );
	$.extend( $.fn.dataTable.defaults,{
			"responsive": true,
			"language": {
				"sProcessing": "Procesando...",
				"sLengthMenu": "Mostrar _MENU_ registros",
				"sZeroRecords": "No se encontraron resultados",
				"sEmptyTable": "Ningún dato disponible en esta tabla",
				"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
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
	
	preparaDonaMorris();
	
	$("#morris-1, #morris-2").resize(function() {
        preparaDonaMorris();
    });
	
	
	// Periodo actual
	var M2 = Morris.Donut({
		element:'morris-2',
		data:[{label:'Aplique un filtro para ver los datos',value:0}],
		formatter: function (y, datos) { return y + '%'; },
		resize:true,
		/*colors: [
				"#3A6603",
				"#FF0000",
				"#F2000D",
				"#D70028",
				"#C90036",
				"#BC0043",
				"#AE0051",
				"#A1005E",
				"#94006B",
				"#860079",
				"#790086",
				"#6B0094",
				"#5E00A1",
				"#5100AE",
				"#4300BC",
				"#3600C9",
				"#2800D7",
				"#1B00E4",
				"#0D00F2",
				"#0000FF",
		],*/
	});
	
	// Periodo anterior
	var M1 = Morris.Donut({
		element:'morris-1',
		data:[{label:'Aplique un filtro para ver los datos',value:0}],
		formatter: function (y, datos) { return y + '%' },
		resize:true,
		/*colors: [
				"#3A6603",
				"#FF0000",
				"#F2000D",
				"#D70028",
				"#C90036",
				"#BC0043",
				"#AE0051",
				"#A1005E",
				"#94006B",
				"#860079",
				"#790086",
				"#6B0094",
				"#5E00A1",
				"#5100AE",
				"#4300BC",
				"#3600C9",
				"#2800D7",
				"#1B00E4",
				"#0D00F2",
				"#0000FF",
		],*/
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
	}
	
	var opcionesLineCharts2 = {
		element:'morris-4',
		data:[{'xkey':fechas.hasta, '1':0}],
		xkey: 'xkey',
		ykeys: ['1'],
		labels: ['No hay datos'],
		yLabelFormat:function (y) { return '$ ' + y.toString(); },
		resize:true,
	}
	
	window.M3 = Morris.Line(opcionesLineCharts1);
	window.M4 = Morris.Line(opcionesLineCharts2);
	
	// Morris barras
	var opcionesMorrisBarras = {
		element: 'morris-barras',
		data: [{'proveedor':'No hay datos', 'suma_anterior':0, 'suma_actual':0}],
		xkey: 'proveedor',
		ykeys: ['suma_anterior', 'suma_actual'],
		labels: ['Anterior', 'Actual']
	}
	
	window.MorrisBarras = Morris.Bar(opcionesMorrisBarras);
	
	
	function totalesPorPeriodo(){
		
		// General
		var filtro_sucursales_array = [];
		$('[name="filtro_sucursales[]"]:checked').each(function(){
			filtro_sucursales_array.push($(this).val());
		});
		
		var ms1 = $("#marketshare1").DataTable({
					retrieve:true,
					order: [[ 2, "desc" ]],
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
					order: [[ 2, "desc" ]],
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
				$("#morris-2-leyenda ul, #morris-4-leyenda ul").html('');
				M2.setData(datos.actual.morris);
				preparaDonaMorris();
				$(datos.actual.morris).each(function(index, item){
					$("#morris-2-leyenda ul, #morris-4-leyenda ul").append($(document.createElement('li')).html('<span class="label label-default" style="display:inline-block; width:45px; text-align:right; margin-right:5px;"> '+item.value+'%</span>'+item.label));
				});
				$("#morris-2-fechas, #morris-4-fechas").html('<h3>Desde '+datos.actual.fechas.desde+' hasta '+datos.actual.fechas.hasta+'</h3>');
				ms2.clear().rows.add(datos.actual.tabla).draw();
				
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
									});
								}
							});

						},
					});
				} );

				//Periodo anterior
				$("#morris-1-leyenda ul, #morris-3-leyenda ul").html('');
				M1.setData(datos.anterior.morris);
				preparaDonaMorris();
				$(datos.anterior.morris).each(function(index, item){
					$("#morris-1-leyenda ul, #morris-3-leyenda ul").append($(document.createElement('li')).html('<span class="label label-default" style="display:inline-block; width:45px; text-align:right; margin-right:5px;"> '+item.value+'%</span>'+item.label));
				});
				$("#morris-1-fechas, #morris-3-fechas").html('<h3>Desde '+datos.anterior.fechas.desde+' hasta '+datos.anterior.fechas.hasta+'</h3>');
				ms1.clear().rows.add(datos.anterior.tabla).draw();
				
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
									});
								}
							});

						},
					});
				} );
				
				
				//jQuery Match Height después de actualizar contenido
				$.fn.matchHeight._update();
				
			},
		});

	}
	
	function histograma(){
		
		// Gráficas: Regresa ambos periodos
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/histogramaMorrisData',
			success:function(datos){
				
				window.M3.options.labels = datos[1].labels;
				window.M3.options.ykeys = datos[1].ykeys;
				window.M3.setData(datos[1].data);
				
				window.M4.options.labels = datos[2].labels;
				window.M4.options.ykeys = datos[2].ykeys;
				window.M4.setData(datos[2].data);
				
			},
		});
		
		// Tablas: Regresa ambos periodos
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/histograma',
			success:function(data){
				$("#histograma").html(data);
				$("#hist-1").DataTable({"order": [[ 0, "desc" ]]});
				$("#hist-2").DataTable({"order": [[ 0, "desc" ]]});
			},
		});
		
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/compararTotalesPorPeriodo',
			success:function(datos){
				
				window.MorrisBarras.setData(datos);
				
				window.MorrisBarras.options.labels.forEach(function(label, i){
					var legendItem = $('<span></span>').text(label).css('color', window.MorrisBarras.options.barColors[i]);
					$("#morris-barras-leyenda").append(legendItem);
				});
				
			},
		});
		
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/compararTotalesPorPeriodoTabla',
			success:function(datos){
				$("#morris-barras-tabla").html(datos);
				$("#morris-barras-tabledata").DataTable();
				
			},
		});

	}
	
	function sesionMonitor(){
		$.ajax({
			type:'post',
			url: constantes.URL + 'Dashboard/sesionMonitor',
			success:function(data){
				$('#sesion-monitor').html(data);
			},
		});	
	}
			
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
	});
	
}

// Morris Donas

$("#morris-1, #morris-2").mouseover(function(){
	preparaDonaMorris();
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

// Prueba
function prueba(){
	alert('Foo');
}
// JavaScript que se ejecuta al cargarse el DOM

$(document).ready(function(){
	"use strict";
	
	// Subfamilias del filtro (selects dependientes) al cargar y al cambiar el select de familias
	obtenerSubfamilias($('#codigo_fam').val());	
	
	$('#codigo_fam').on("click change", this, function(){
		obtenerSubfamilias($(this).val());	
	});
	
	// Botón del filtro
	/*$('#gui_btn_filtro').click(function(e){
		totalesPorPeriodo();
		e.preventDefault();
	});	*/

	// DATATABLES defaults en general
	$.extend( $.fn.dataTable.defaults,{
			"responsive": true,
			//"serverSide": true,
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
			data:[
				['0','0','0']
			]
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
		colors: [
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
		],
	});
	
	// Periodo anterior
	var M1 = Morris.Donut({
		element:'morris-1',
		data:[{label:'Aplique un filtro para ver los datos',value:0}],
		formatter: function (y, datos) { return y + '%' },
		resize:true,
		colors: [
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
		],
	});
	
	function totalesPorPeriodo(){
		
		// General
		var json2, json1;
		var filtro_sucursales_array = [];
		$('[name="filtro_sucursales[]"]:checked').each(function(){
			filtro_sucursales_array.push($(this).val());
		});
		
		//Periodo actual
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
			success:function(datos2){
				$("#morris-2-leyenda ul").html('');
				json2 = datos2;
				M2.setData(json2.morris);
				var dataSet = json2.tabla;
				preparaDonaMorris();
				$(json2.morris).each(function(index, item){
					$("#morris-2-leyenda ul").append($(document.createElement('li')).html('<span class="label label-default" style="display:inline-block; width:45px; text-align:right; margin-right:5px;"> '+item.value+'%</span>'+item.label));
				});
				$("#dt2").DataTable({
					retrieve:true,
					data: dataSet,
					order: [[ 1, "desc" ]],
					columns: [
						{ title: "Proveedor" },
						{ title: "Venta" },
						{ title: "Market Share" },
					],
					
					initComplete: function () {
						var api = this.api();
						api.$('tr').click( function () {
							console.log($(this).find('td:first-child').text());
						} );
					}					
				});
			},
		});
		
		//Periodo anterior
		$.ajax({
			method: "POST",
			url: constantes.URL+"Dashboard/totalesPorPeriodo",
			data: {
				filtro_sucursales:filtro_sucursales_array,
				codigo_fam: $('#codigo_fam').val(),
				codigo_sub: $('#codigo_sub').val(),
				rangodefechas:$('#rangodefechas').val(),
				periodo:$('[name="periodo"]:checked').val(),
				cual_periodo:"anterior",
			},
			success:function(datos1){
				$("#morris-1-leyenda ul").html('');
				json1 = datos1;
				M1.setData(json1.morris);
				var dataSet = json1.tabla;
				preparaDonaMorris();
				$(json1.morris).each(function(index, item){
					$("#morris-1-leyenda ul").append($(document.createElement('li')).html('<span class="label label-default" style="display:inline-block; width:45px; text-align:right; margin-right:5px;"> '+item.value+'%</span>'+item.label));
				});
				$("#dt1").DataTable({
					retrieve:true,
					data: dataSet,
					order: [[ 1, "desc" ]],
					columns: [
						{ title: "Proveedor" },
						{ title: "Venta" },
						{ title: "Market Share" },
					],
					
					initComplete: function () {
						var api = this.api();
						api.$('tr').click( function () {
							console.log($(this).find('td:first-child').text());
						} );
					}					
					
				});
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
            fechas.desde,
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
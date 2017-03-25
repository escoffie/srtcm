// Bootstrap Tour
// http://bootstraptour.com/

var tour = new Tour({
	template: '<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-default" data-role="prev">&laquo; Ant</button> <button class="btn btn-sm btn-default" data-role="next">Sig &raquo;</button> <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pausa</button> </div> <button class="btn btn-sm btn-default" data-role="end">Terminar</button> </div> </div>',
	smartPlacement: false,
	backdropPadding:10,
	steps: [
		{
			element: "#bienvenida-01",
			title: "Bienvenido a Surticoma B2B",
			content: "<p>Este sistema le permitirá analizar su desempeño con respecto a su competencia en un rango de fechas determinado. En este sencillo tour le mostraremos los aspectos básicos del uso de esta herramienta.</p><p><strong>¿Continuamos?</strong></p>",
			orphan:true,
			backdrop:true,
			onShown:function(){
				$('a[aria-controls="perfil"]').tab('show');
			},
		},
		{
			element: 'a[aria-controls="perfil"]',
			title: "Su perfil",
			content: "<p>Es importante que mantenga su perfil actualizado.</p><p>Por favor, tómese un minuto para completar la siguente información. Esto sólo se tiene qué hacer una vez.</p>",
			placement:'bottom',
			onShow:function(){
				$('a[aria-controls="perfil"]').tab('show');
			},
		},
		{
			element: "#tour-perfil-01",
			title: "Datos de la compañía",
			content: "<p>Estos datos no se pueden modificar, pero se le solicitarán en caso de que requiera soporte técnico.</p><p>Si su RFC o razón social son incorrectos, solicite a su ejecutivo de cuenta que se los actualice.</p>",
			placement:"top",
			backdrop:true,
		},
		{
			element: "#tour-perfil-02",
			title: "Datos de contacto",
			content: "<p>Por favor, complete su dirección postal, teléfono de contacto y correo electrónico.</p><p>Su <strong>correo electrónico</strong> le servirá como nombre de usuario para acceder a este sistema.</p>",
			placement:"top",
			backdrop:true,
		},
		{
			element: "#tour-perfil-03",
			title: "Contraseña",
			content: "Por favor, cambie la contraseña que le asignamos por una que contenga números, letras en minúsculas y mayúsculas.",
			placement:"top",
			backdrop:true,
		},
		{
			element: "#tour-filtro-01",
			title: "Acerca del filtro",
			content: "<p>Antes de comenzar, debe seleccionar los datos que desea revisar.</p><p>Esto se hace, eligiendo un intervalo de tiempo, una o más sucursales, así como una familia y subfamilia.</p><p>Es importante señalar que si su plan de visibilidad es básico para cualquiera de las sucursales, al seleccionar una sucursal con dicho plan, <em>todos los reportes se comportarán como plan básico</em>.</p><p><strong>Cuando esté listo, haga clic en el botón 'Filtrar' y espere a que se carguen los datos.</strong></p>",
			backdrop:true,
			placement:"right",
			template: '<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-default" data-role="prev">&laquo; Ant</button> <button disabled class="btn btn-sm btn-default" data-role="next">Sig &raquo;</button> <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pausa</button> </div> <button class="btn btn-sm btn-default" data-role="end">Terminar</button> </div> </div>',
			onShow:function(){ 
				$('.navbar-fixed-top').addClass('sidebar-tour-fix');
				$('.sidebar').addClass('sidebar-tour-fix');
			},
			onHide:function(){ 
				$('.navbar-fixed-top').removeClass('sidebar-tour-fix');
				$('.sidebar').removeClass('sidebar-tour-fix');
			},
		},
		{
			element: '#espera',
			title: "Cargando datos",
			content: "<p>Es normal que los datos tarden algunos segundos en cargarse.</p><p>Por favor, sea paciente</p>",
			backdrop:true,
			orphan:true,
			duration:7000,
		},
		{
			element: 'a[aria-controls="market-share"]',
			title: "Market Share",
			content: "<p>Aquí podrá ver su marca comparada con sus competidores en el rango de fechas seleccionado, comparado con el rango de fechas anterior.</p><p>Use el botón 'Ver más' en las tablas que se encuentran debajo de los gráficos para ver más detalles.</p>",
			placement:"bottom",
		},
		{
			element: 'a[aria-controls="venta-por-dia"]',
			title: "Venta por día",
			content: "<p>Este reporte despliega la venta por día, por competidor, comparando contra el periodo anterior.</p><p>Haga clic en la pestaña para ver los reportes de venta por día.</p>",
			placement:"bottom",
		},
		{
			element: 'a[aria-controls="comparativo"]',
			title: "Análisis comparativo",
			content: "<p>Aquí podrá ver su venta por artículo, por presentación, y compararla contra cualquiera de sus competidores, para el rango de fechas seleccionado.</p><p>Haga clic en la pestaña para ver los reportes comparativos.</p>",
			placement:"bottom",
		},
		{
			element: "#final",
			title: "¡Muchas gracias!",
			content: "<p>Si tiene dudas, puede volver a tomar este tour, yendo arriba a la derecha, a su nombre, y luego haciendo clic en 'Cómo funciona', o bien puede encontrar los datos de contacto al pie de la página. También puede recurrir al chat que se encuentra abajo a la derecha.</p>",
			orphan:true,
			backdrop:true,
			onShown:function(){
				$('a[aria-controls="perfil"]').tab('show');
			},
		},
	],
});

// Initialize the tour
tour.init();

// Start the tour
tour.start();

//Para que avance al hacer clic en "Filtrar"
//Esto se movió a dashboard-acciones.js
/*$('#gui_btn_filtro').click(function(){
	$('a[aria-controls="market-share"]').tab('show');
	tour.next();
});*/

// Para ejecutar el tour vía clic
$('#tour-start').click(function(e){
	e.preventDefault();
	tour.init();
	tour.start(true);
	tour.goTo(0);
	
	bitacora('Interacción', 'Mostrar tour');
	
});
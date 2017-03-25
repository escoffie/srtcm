<?php

class Dashboard extends Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		$filtroSucursales 	= $this->model->sucursalesPorProveedor($_SESSION['usuario']['codigo_pro']);
		$filtroFamilias 	= $this->model->familiasPorProveedor($_SESSION['usuario']['codigo_pro']);

		if(!isset($_SESSION['filtro'])){
			$fechas = $this->model->fechasParaDateRangePicker();
			$_SESSION['filtro']['fechaA_desde'] = $fechas[0]['desde'];
			$_SESSION['filtro']['fechaA_hasta'] = $fechas[0]['hasta'];
		}

		$data = array(
			'filtroSucursales'=>$filtroSucursales,
			'filtroFamilias'=>$filtroFamilias,
		);
		
		$this->view->render($this, 'index', $data);
	}
	
	function subfamiliasPorFamiliaPorProveedor($codigo_fam) {
		$filtroSubfamilias 	= $this->model->subfamiliasPorFamiliaPorProveedor($codigo_fam, $_SESSION['usuario']['codigo_pro']);
		foreach($filtroSubfamilias as $subfamilia){
			if(isset($_SESSION['filtro']['codigo_sub']) and $subfamilia['codigo_sub'] == $_SESSION['filtro']['codigo_sub']){
				$checked = 'selected';
			} else {
				$checked = '';	
			}
		?>
		<option <?php echo $checked; ?> value="<?php echo $subfamilia['codigo_sub']; ?>"> <?php echo $subfamilia['nombre_sub']; ?></option>
		<?php
		} 
	}
		
	function totalesPorPeriodo(){
		
		Session::setValue('filtro', $_POST);
		
		$fechasA = explode(' - ',$_POST['rangodefechas']);
		
		$_SESSION['filtro']['fechaA_desde'] = $_POST['fechaA_desde'] = $fechasA[0];
		$_SESSION['filtro']['fechaA_hasta'] = $_POST['fechaA_hasta'] = $fechasA[1];
		
		if($_POST['periodo'] == 1){
			
			$_SESSION['filtro']['fechaB_desde'] = $_POST['fechaB_desde'] = date('Y-m-d', strtotime($fechasA[0] . '- 1 year'));
			$_SESSION['filtro']['fechaB_hasta'] = $_POST['fechaB_hasta'] = date('Y-m-d', strtotime($fechasA[1] . '- 1 year'));
			
		} else {
			
			$d1 = new DateTime($_POST['fechaA_desde']);
			$d2 = new DateTime($_POST['fechaA_hasta']);
			
			$diferencia = $d2->diff($d1)->format("%a");
			
			$_SESSION['filtro']['fechaB_desde'] = $_POST['fechaB_desde'] = date('Y-m-d', strtotime($fechasA[0] . '- '.($diferencia+1).' days'));
			$_SESSION['filtro']['fechaB_hasta'] = $_POST['fechaB_hasta'] = date('Y-m-d', strtotime($fechasA[1] . '- '.($diferencia+1).' days'));
			
		}
		
		
		$_POST['where_sucursales'] = implode(',', $_POST['filtro_sucursales']);
			
		$totalesPorPeriodo = $this->model->totalesPorPeriodo($_POST);
		
		header('Content-type: application/json');
		echo json_encode($totalesPorPeriodo);
		
	}
	

	function ventaPorDia(){
		extract($_SESSION['filtro']);
		$resultado = $this->model->ventaPorDia($_POST['codigo_pro'], $codigo_fam, $codigo_sub, $_POST['fecha_ini'], $_POST['fecha_fin'], implode(',',$filtro_sucursales));
		?>
        <table class="table table-hover">
            <theader>
                <tr>
                    <th>Código de barra</th>
                    <th>Artículo</th>
                    <th>Capacidad</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Cantidad</th>
                    <th>&nbsp;</th>
                </tr>
            </theader>
            <tbody>
            <?php
			$i=1;
			foreach($resultado as $fila => $celda) {
				$unidad = $celda['unidad'];
				if($celda['cantidad']==1 and $celda['unidad']=='U'){
					$unidad = 'unidad';
				} else if ($celda['cantidad']>1 and $celda['unidad']=='U'){
					$unidad = 'unidades';
				}
			?>
                <tr class="verDetalleTickets clickable" data-toggle="collapse" id="row<?php echo $i; ?>" data-target=".row<?php echo $i; ?>" data-id="<?php echo $i; ?>" data-codigoart="<?php echo $celda['codigo_art']; ?>" data-fecha="<?php echo $celda['fecha']; ?>" data-sucursales="<?php echo implode(',',$filtro_sucursales); ?>">
                    <td><?php echo $this->model->ocultarNombreParaNivelUno($_POST['codigo_pro'], $celda['barcode_caja'], '[Código de barra '.$i.']'); ?></td>
                    <td><?php echo $this->model->ocultarNombreParaNivelUno($_POST['codigo_pro'], $celda['nombre'], '[Artículo '.$i.']'); ?></td>
                    <td><?php echo $celda['capacidad']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($celda['fecha'])); ?></td>
                    <td><span class="label label-success">$ <?php echo money_format($celda['monto'],2); ?></span></td>
                    <td><?php echo $celda['cantidad']; ?> <?php echo $unidad; ?></td>
                    <td><i class="fa fa-caret-down"> </i></td>
                </tr>
                <tr class="collapse row<?php echo $i; ?>" id="toggle-<?php echo $i; ?>">
                	<td colspan="2">&nbsp;</td>
                    <td colspan="5">
                    	<table class="table table-condensed">
                        	<?php
							// 	$celda['codigo_art'], $celda['fecha'], implode(',',$filtro_sucursales)
							?>
                            <thead>
                        		<tr>
                        			<th>Folio ticket</th>
                        			<th>Monto</th>
                        			<th>Cantidad</th>
                        		</tr>
                        	</thead>
                            <tbody class="insertarespuestaaqui" id="tickets-<?php echo $i; ?>">
                            	<tr>
                            		<td colspan="3" class="text-center">
                                    	<p>Cargando...</p>
                                        <img src="<?php echo URL; ?>public/images/spinner.gif" alt="Cargando">
                                    </td>
                            	</tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php
				$i++;
			}
			?> 
            </tbody>
        </table>
        <?php
		/*echo "<pre>";
		print_r($resultado);
		echo "</pre>";*/
	}
	
	function ticketsPorArticuloPorDia(){
		$detalletickets = $this->model->ticketsPorArticuloPorDia($_POST['codigo_art'], $_POST['fecha'], $_POST['sucursales']);
		foreach($detalletickets as $detallefila => $detallecelda){
		?>
		<tr>
			<td><?php echo $detallecelda['folio']; ?></td>
			<td>$ <?php echo money_format($detallecelda['monto'],2); ?></td>
			<td align="center"><?php echo $detallecelda['cantidad']; ?></td>
		</tr>
		<?php
		}
	}
	
	function histograma(){
		if(empty($_POST)){
			$fecha_ini='2016-01-01';
			$fecha_fin='2016-04-15';
			$sucursales='1001,2219';
			$codigo_sub=639;
		} else {
			extract($_POST);	
		}
		$resultado = $this->model->histograma($fecha_ini, $fecha_fin, $sucursales, $codigo_sub);
		
		?>
        <table class="table table-striped table-bordered table-hover dataTables-activar display">
        	<thead class="header">
            	<tr>
                	<th>Día</th>
            	<?php
				foreach($resultado['theader'] as $columna => $valor){
					?>
                    <th><?php echo $valor; ?></th>
                    <?php	
				}
				?>
                </tr>
            </thead>
        	<tfoot class="footer">
            	<tr>
                	<th>Total</th>
            	<?php
				foreach($resultado['tfooter'] as $columna => $valor){
					?>
                    <th><?php echo $valor; ?></th>
                    <?php	
				}
				?>
                </tr>
            </tfoot>
            <tbody>
            	
				<?php
				foreach($resultado as $fila => $celdas){
					if($fila!='theader' and $fila!='tfooter'){
					?>
					<tr>
						<th><?php echo date('Y-m-d', strtotime($fila)); ?></th>
						<?php
						foreach($celdas as $celda){
							?>
							<td align="right"><?php echo $celda; ?></td>
							<?php	
						}
					?>
					</tr>
					<?php
					}
				}
				?>
            </tbody>
        </table>
        <?php
		
		/*
		echo "<pre>";
		print_r($resultado);
		echo "</pre>";
		*/
	}
	
}
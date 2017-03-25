<?php


class Dashboard extends Controller {

	function __construct(){
		parent::__construct();
		if( !Session::exists() ) {
			$location = '';
			if(!empty($_SERVER['REQUEST_URI'])) $location = "?r=".urlencode($_SERVER['REQUEST_URI']);
			header("Location: ".URL);
		}
	}

	function index(){
		$filtroSucursales 	= $this->model->sucursalesPorProveedor($_SESSION['usuario']['codigo_pro']);
		$filtroFamilias 	= $this->model->familiasPorProveedor($_SESSION['usuario']['codigo_pro']);
		$perfil				= $this->model->perfil($_SESSION['usuario']['codigo_pro']);

		if( Session::exists() ) {
			if(!isset($_SESSION['filtro'])){
				$fechas = $this->model->fechasParaDateRangePicker();
				$_SESSION['filtro']['fechaA_desde'] = $fechas[0]['desde'];
				$_SESSION['filtro']['fechaA_hasta'] = $fechas[0]['hasta'];
			}
	
			$data = array(
				'filtroSucursales'=>$filtroSucursales,
				'filtroFamilias'=>$filtroFamilias,
				'perfil'=>$perfil,
				'submenu'=>'Dashboard_filtro',
			);
			
			$this->view->render($this, 'index', $data);
		} else {
			$location = '';
			if(!empty($_SERVER['REQUEST_URI'])) $location = "?r=".urlencode($_SERVER['REQUEST_URI']);
			//header("Location: ".URL.$location."#acceder");
			header("Location: ".URL);
		}

	}
	
	function perfilUpdate(){
		$resultado = $this->model->perfilUpdate($_POST);
		if($resultado){
			header('Content-type: application/json');
			echo json_encode($resultado);
		}
	}
		
	function subfamiliasPorFamiliaPorProveedor($codigo_fam) {
		$filtroSubfamilias 	= $this->model->subfamiliasPorFamiliaPorProveedor($codigo_fam, $_SESSION['usuario']['codigo_pro']);
		if($_SESSION['usuario']['codigo_pro']>0){
		?>
        <option value=""> Seleccione una</option>
        <?php
		}
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
	
	function filtro(){
		
		Session::setValue('filtro', $_POST);
		
		// Nivel de acceso, con base en el nivel más bajo de las sucursales seleccionadas
		$_SESSION['usuario']['id_niv'] = $_POST['id_niv'];
		
		// Sucursales como cadena separada por comas para FIND_IN_SET en las consultas MySQL
		$_SESSION['filtro']['where_sucursales'] = implode(',',$_POST['filtro_sucursales']);

		//$_SESSION['filtro']['where_sucursales'] = $_POST['filtro_sucursales'];

		
		$fechasA = explode(' - ',$_POST['rangodefechas']);
		
		// Periodo seleccionado
		$_SESSION['filtro']['fechaA_desde'] = $fechasA[0];
		$_SESSION['filtro']['fechaA_hasta'] = $fechasA[1];
		
		// Periodo anterior, según la selección (año anterior, adyacente anterior)
		if($_POST['periodo'] == 1){
			
			// Año anterior
			$_SESSION['filtro']['fechaB_desde'] = date('Y-m-d', strtotime($fechasA[0] . '- 1 year'));
			$_SESSION['filtro']['fechaB_hasta'] = date('Y-m-d', strtotime($fechasA[1] . '- 1 year'));
			
		} else {
			
			// Adyacente anterior
			$d1 = new DateTime($fechasA[0]);
			$d2 = new DateTime($fechasA[1]);
			
			$diferencia = $d2->diff($d1)->format("%a");
			
			$_SESSION['filtro']['fechaB_desde'] = date('Y-m-d', strtotime($fechasA[0] . '- '.($diferencia+1).' days'));
			$_SESSION['filtro']['fechaB_hasta'] = date('Y-m-d', strtotime($fechasA[1] . '- '.($diferencia+1).' days'));
			
		}
		
	}
		
	/*
	* totalesPorPeriodo()
	* PESTAÑA: MARKET SHARE
	* APARECE: Fuente de datos para las donas y para las DataTables de Market Share
	*/
	function totalesPorPeriodo(){
			
		$totalesPorPeriodo = $this->model->totalesPorPeriodo($_SESSION['filtro']);
		
		header('Content-type: application/json');
		echo json_encode($totalesPorPeriodo);
		
	}
	

	/*
	* ventaPorDia()
	* PESTAÑA: MARKET SHARE
	* APARECE: al hacer clic en los botones "ver más" de las DataTables, debajo de las donas
	*/
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
                    <td><?php echo $this->model->ocultarNombreParaNivelUno($_POST['codigo_pro'], $celda['barcode_pieza'], '[Código de barra '.$i.']'); ?></td>
                    <td><?php echo $this->model->ocultarNombreParaNivelUno($_POST['codigo_pro'], $celda['nombre'], '[Artículo '.$i.']'); ?></td>
                    <td><?php echo $celda['capacidad']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($celda['fecha'])); ?></td>
                    <td><span class="label label-success">$ <?php echo money_format($celda['monto'],2); ?></span></td>
                    <td><?php echo $celda['cantidad']; ?> <?php echo $unidad; ?></td>
                    <td><i class="fa fa-caret-down"> </i></td>
                </tr>
                <tr class="collapse row<?php echo $i; ?>" id="toggle-<?php echo $i; ?>">
                	<td colspan="1">&nbsp;</td>
                    <td colspan="6">
                    	<table class="table table-condensed">
                        	<?php
							// 	$celda['codigo_art'], $celda['fecha'], implode(',',$filtro_sucursales)
							?>
                            <thead>
                        		<tr>
                        			<th>Folio ticket</th>
                        			<th>Monto</th>
                        			<th>Cantidad</th>
                        			<th>Sucursal</th>
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
			<td><?php echo $detallecelda['nombre_suc']; ?></td>
		</tr>
		<?php
		}
	}
	
	function histogramaMorrisData(){
			
		$fechaA_ini = $_SESSION['filtro']['fechaA_desde'];
		$fechaA_fin = $_SESSION['filtro']['fechaA_hasta'];
		$fechaB_ini = $_SESSION['filtro']['fechaB_desde'];
		$fechaB_fin = $_SESSION['filtro']['fechaB_hasta'];
		$sucursales = $_SESSION['filtro']['where_sucursales'];
		$codigo_sub = $_SESSION['filtro']['codigo_sub'];
		$codigo_pro = $_SESSION['usuario']['codigo_pro'];

		$resultados[] = $this->model->histograma($fechaB_ini, $fechaB_fin, $sucursales, $codigo_sub, $codigo_pro);
		$resultados[] = $this->model->histograma($fechaA_ini, $fechaA_fin, $sucursales, $codigo_sub, $codigo_pro);
		
		$i=0;
		foreach($resultados as $resultado){
			if(sizeof($resultado)>0){
				$i++;
				$morris[$i] = array();
				$ykey = 0;
				foreach($resultado['theader'] as $columna => $valor){
					$ykey++;
					$morris[$i]['ykeys'][] = (string)$ykey;
					$morris[$i]['labels'][] = $valor;
				}
				foreach($resultado['tfooter'] as $columna => $valor){
					$morris[$i]['totales'][] = $valor;	
				}
				$morris[$i]['data'] = array();
				foreach($resultado as $fila => $celdas){
					if($fila!='theader' and $fila!='tfooter'){
						$estafila = array();
						$estafila['xkey'] = $fila;
						$iii=0;
						foreach($celdas as $celda){
							$iii++;
							$estafila[$iii] = $celda;
						}
						array_push($morris[$i]['data'], $estafila);
					}
				}
			}
			
		}
		// Termina for each
		/**/
		header('Content-Type: application/json');
		echo json_encode($morris);
		
	}
	
	function histograma(){
			
		$fechaA_ini = $_SESSION['filtro']['fechaA_desde'];
		$fechaA_fin = $_SESSION['filtro']['fechaA_hasta'];
		$fechaB_ini = $_SESSION['filtro']['fechaB_desde'];
		$fechaB_fin = $_SESSION['filtro']['fechaB_hasta'];
		$sucursales = $_SESSION['filtro']['where_sucursales'];
		$codigo_sub = $_SESSION['filtro']['codigo_sub'];
		$codigo_pro = $_SESSION['usuario']['codigo_pro'];

		$resultados[] = $this->model->histograma($fechaB_ini, $fechaB_fin, $sucursales, $codigo_sub, $codigo_pro);
		$resultados[] = $this->model->histograma($fechaA_ini, $fechaA_fin, $sucursales, $codigo_sub, $codigo_pro);
		
		$i=0;
		foreach($resultados as $resultado){
			if(sizeof($resultado)>0){
			$i++;
		?>
        <div class="dataTable_wrapper col-md-6">
            <table class="table table-striped table-bordered table-hover dataTable display nowrap" width="100%" id="hist-<?php echo $i; ?>" align="center">
                <thead class="header">
                    <tr>
                        <th>Día</th>
                    <?php
					if(sizeof($resultado)>0){
						foreach($resultado['theader'] as $columna => $valor){
							?>
							<th><span data-toggle="tooltip" data-placement="bottom" title="<?php echo $valor; ?>"><?php echo $this->model->elipsis($valor); ?></span></th>
							<?php	
						}
					}
                    ?>
                    </tr>
                </thead>
                <tfoot class="footer">
                    <tr>
                        <th>Total</th>
                    <?php
					if(sizeof($resultado)>0){
						foreach($resultado['tfooter'] as $columna => $valor){
							?>
							<td align="center"><b><?php echo $this->model->moneda($valor); ?></b></td>
							<?php	
						}
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
                            <th nowrap><?php echo date('d/m/Y', strtotime($fila)); ?></th>
                            <?php
                            foreach($celdas as $celda){
                                ?>
                                <td align="center"><?php echo $this->model->moneda($celda); ?></td>
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
        </div>
        <?php
			/*
			echo "<pre>";
			print_r($resultado);
			echo "</pre>";
			*/
			}
		}
		// Termina for each
		
	}
	
	function compararTotalesPorPeriodo(){
		$arreglo = $this->model->compararTotalesPorPeriodo($_SESSION['filtro']['fechaA_desde'], $_SESSION['filtro']['fechaA_hasta'],$_SESSION['filtro']['fechaB_desde'], $_SESSION['filtro']['fechaB_hasta'], $_SESSION['filtro']['codigo_fam'], $_SESSION['filtro']['codigo_sub'], $_SESSION['usuario']['codigo_pro'], $_SESSION['filtro']['where_sucursales']);
		/*echo "<pre>";
		print_r($arreglo);*/
		header('Content-Type: application/json');
		echo json_encode($arreglo);
	}
	
	function compararTotalesPorPeriodoTabla(){
		$arreglo = $this->model->compararTotalesPorPeriodo($_SESSION['filtro']['fechaA_desde'], $_SESSION['filtro']['fechaA_hasta'],$_SESSION['filtro']['fechaB_desde'], $_SESSION['filtro']['fechaB_hasta'], $_SESSION['filtro']['codigo_fam'], $_SESSION['filtro']['codigo_sub'], $_SESSION['usuario']['codigo_pro'], $_SESSION['filtro']['where_sucursales']);
		?>
        <table class="table table-striped table-bordered table-hover dataTable display nowrap" width="100%" id="morris-barras-tabledata">
        	<thead>
            	<tr>
            		<th>Proveedor</th>
            		<th>Periodo anterior</th>
            		<th>Periodo actual</th>
            		<th>Variación</th>
            	</tr>
            </thead>
            <tbody>
            	<?php
				$suma_anterior = 0;
				$suma_actual = 0;
				$suma_porcen = 0; //17022017 ASUNZA SE AGREGO EL TOTAL DE LOS PORCENTAJES
				$i=0;
				foreach($arreglo as $fila){
					$i++;
					$suma_anterior += $fila['suma_anterior'];
					$suma_actual += $fila['suma_actual'];
				?>
                <tr>
            		<td><?php echo $fila['proveedor']; ?></td>
            		<td align="center"><?php echo $this->model->moneda($fila['suma_anterior']); ?></td>
            		<td align="center"><?php echo $this->model->moneda($fila['suma_actual']); ?></td>
            		<td align="center"><?php
					if($fila['suma_actual']>0 and $fila['suma_anterior']>0) 
					$diferencia = ($fila['suma_anterior'] - $fila['suma_actual'])/$fila['suma_anterior']*-100;
					else if($fila['suma_anterior']==0) $diferencia = 100;
					else if($fila['suma_actual']==0) $diferencia = -100;
					echo round($diferencia,2);
					?> %</td>
            	</tr>
                <?php	
				}
				if($suma_anterior>0 and $suma_actual>0) 
				$suma_porcen = ($suma_anterior - $suma_actual) / $suma_anterior * -100;
				else if($suma_anterior==0) $suma_porcen = 100;
				else if($suma_actual==0) $suma_porcen = -100;
				?>
            </tbody>
            <tfoot>
            	<tr>
            		<td><b>Totales</b></td>
            		<td align="center"><b><?php echo $this->model->moneda($suma_anterior); ?></b></td>
            		<td align="center"><b><?php echo $this->model->moneda($suma_actual); ?></b></td>
                    <td align="center"><b><?php echo round($suma_porcen,2); ?> %</b></td> <!-- 17022017 ASUNZA -->
            	</tr>
            </tfoot>
        </table>
        <?php
		/*$b = call_user_func_array(
			'array_map',
			//array(-1 => null) + array_map('array_reverse', $arreglo)
			array(-1 => null) + $arreglo
		);
		echo "<pre>";
		print_r($arreglo);
		print_r($b);
		echo "</pre>";*/
	}
	
	### COMPARATIVO ###
	
	function comparativoDropdown(){
		?>
        <?php
		$elementos = $this->model->comparativoDropdown();
		$i=0;
		if(sizeof($elementos)>0){
			?>
            <option value="0" selected>Seleccione de la lista</option>
            <?php
			foreach($elementos as $elemento){ 
				$selected='';
				if($elemento['codigo_pro']==$_SESSION['usuario']['codigo_pro']) $selected='selected';
			?>
			<option <?php echo $selected; ?> value="<?php echo $elemento['codigo_pro']; ?>"><?php echo $this->model->ocultarNombreParaNivelUno($elemento['codigo_pro'], $elemento['proveedor'], "[Competidor $i]"); ?></option>
			<?php
				$i++;
			}
		} else {
			echo 0;
		}
		
		//echo "<pre>";
		//print_r($elementos);
	}
	
	function comparativoDataMorris($codigo_pro=''){
		if($codigo_pro=='') $codigo_pro=$_POST['codigo_pro'];
		$arreglo = $this->model->comparativoDataMorris($codigo_pro);
		header('Content-Type: application/json');
		echo json_encode($arreglo);
	}
	
	function comparativoDataTable($codigo_pro=''){
		if($codigo_pro=='') $codigo_pro=$_POST['codigo_pro'];
		$arreglo = $this->model->comparativoDataTable($codigo_pro);
		header('Content-Type: application/json');
		echo json_encode($arreglo);
	}
	
	function monitorFiltro(){
		$resultado = $this->model->monitorFiltro();
		?>
        <div class="alert alert-info">
            <ul class="list-inline">
                <li><strong>Intervalo de fechas:</strong> <?php echo $resultado['fechas']; ?></li>
                <li><strong>Sucursales:</strong> <?php echo $resultado['sucursales']; ?></li>
                <?PHP if($_SESSION['usuario']['codigo_pro']<0){ ?><li><strong>Familia:</strong> <?php echo $resultado['familia']; ?></li><?php  }?>
                <li><strong>Categoría:</strong> <?php echo $resultado['subfamilia']; ?></li>
            </ul>
        </div>
        <?php
		if($_SESSION['usuario']['id_niv']==1){
		?>
        <div class="alert alert-warning alert-dismissible" role="alert">
        	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        	<p><strong>Importante: </strong>Los reportes ocultarán los datos de sus competidores, debido a que al menos alguna de las sucursales seleccionadas cuenta con <strong>nivel básico de acceso</strong>. Si desea ver los datos de sus competidores, le recomendamos contactar con un ejecutivo para mejorar su nivel de acceso.</p>
        </div>
        <?php
		}
		?>
        <?php	
	}
	
	function excel(){
		require ('./Classes/PHPExcel.php');
		$resultado = $this->model->monitorFiltro();
		$datos = $this->model->excel();
		
		/*echo '<pre>';
		print_r($datos);
		die();*/
		
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Surticoma")
									 ->setLastModifiedBy("Surticoma")
									 ->setTitle("Reporte generado por el CRM Surticoma el ".date('Y-m-d H:i:s'))
									 ->setSubject("Reporte generado por el CRM Surticoma el ".date('Y-m-d H:i:s'))
									 ->setDescription("Intervalo de fechas: $resultado[fechas]. Sucursales: $resultado[sucursales]. Familia: $resultado[familia]. Subfamilia: $resultado[subfamilia]. Reporte generado el ".date('Y-m-d H:i:s'))
									 ->setKeywords("surticoma reporte")
									 ->setCategory("Surticoma reporte");


		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Transacción')
					->setCellValue('B1', 'Monto')
					->setCellValue('C1', 'Estado')
					->setCellValue('D1', 'Región')
					->setCellValue('E1', 'Sucursal')
					->setCellValue('F1', 'Tipo')
					->setCellValue('G1', 'Familia')
					->setCellValue('H1', 'Subfamilia')
					->setCellValue('I1', 'Compañía')
					->setCellValue('J1', 'Artículo')
					->setCellValue('K1', 'Capacidad')
					->setCellValue('L1', 'Empaque')
					->setCellValue('M1', 'Unidad de medida')
					->setCellValue('N1', 'Código de barras')
					->setCellValue('O1', 'Fecha de facturación')
					->setCellValue('P1', 'Cantidad');
		
		// Rellenar de datos
		$i=2;
		foreach($datos as $dato){
			$f_Y = date('Y', strtotime($dato['Fecha']));
			$f_m = date('m', strtotime($dato['Fecha']));
			$f_d = date('d', strtotime($dato['Fecha']));
			$fecha = PHPExcel_Shared_Date::FormattedPHPToExcel($f_Y, $f_m, $f_d);
			//$this->model->ocultarNombreParaNivelUno($elemento['codigo_pro'], $elemento['proveedor'], "[Competidor $i]")
			$objPHPExcel->getActiveSheet()
					->setCellValueExplicit('A'.$i, $dato['Transacción'],PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValue('B'.$i, $dato['Monto'])
					->setCellValue('C'.$i, $dato['Estado'])
					->setCellValue('D'.$i, $dato['Región'])
					->setCellValue('E'.$i, $dato['Sucursal'])
					->setCellValue('F'.$i, $dato['Tipo'])
					->setCellValue('G'.$i, $dato['Familia'])
					->setCellValue('H'.$i, $dato['Subfamilia'])
					->setCellValue('I'.$i, $dato['Compañía'])
					->setCellValue('J'.$i, $dato['Artículo'])
					->setCellValue('K'.$i, $dato['Capacidad'])
					->setCellValue('L'.$i, $dato['Empaque'])
					->setCellValue('M'.$i, $dato['Unidad de medida'])
					->setCellValueExplicit('N'.$i, $dato['Código de barra'],PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValue('O'.$i, $fecha)
					->setCellValue('P'.$i, $dato['Cantidad']);
			// Formato monto
			$objPHPExcel->getActiveSheet()
						->getStyle('B'.$i)
						->getNumberFormat()
						->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE   );
			// Formato fecha
			$objPHPExcel->getActiveSheet()
						->getStyle('O'.$i)
						->getNumberFormat()
						->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY );
			$i++;
		}
		
		// Ancho automático de columnas
		for($col = 'A'; $col !== 'P'; $col++) {
			$objPHPExcel->getActiveSheet()
				->getColumnDimension($col)
				->setAutoSize(true);
		}	
		
		$objPHPExcel->getActiveSheet()->freezePane('A2');
		$objPHPExcel->getActiveSheet()->setAutoFilter('A1:P1');
			
		// Formato
		$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->applyFromArray(
			array(
				'fill'=>array(
					'type'=>PHPExcel_Style_Fill::FILL_SOLID, 
					'color'=>array('argb'=>'FFCCFFCC')
				),
				'borders'=>array(
					'bottom'=>array('style'=>PHPExcel_Style_Border::BORDER_MEDIUM)
				)
			)
		);
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Reporte Surticoma');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="surticoma-reporte-'.date('Y-m-d').'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
				
		
		/*	
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8;");
		header("Content-Disposition: attachment; filename=surticoma-reporte-".date('Y-m-d').".xls");
		//print_r($datos);
		$encabezados = false;
		foreach($datos as $dato){
			if($encabezados==false){
				echo utf8_decode(implode("\t", array_keys($dato))). "\r\n";
				$encabezados=true;
			}
			echo implode("\t", array_values($dato)). "\r\n";
		}
		//print_r($datos);
		*/
	}
	
	
	
	function proveedores($parametro=''){
		if($_SESSION['usuario']['codigo_pro']!=0) die("No tiene acceso a esta sección");
		$filtroSucursales 	= $this->model->sucursalesPorProveedor($_SESSION['usuario']['codigo_pro']);
		$filtroFamilias 	= $this->model->familiasPorProveedor($_SESSION['usuario']['codigo_pro']);
		$perfil				= $this->model->perfil($_SESSION['usuario']['codigo_pro']);

		if( Session::exists() ) {
			if(!isset($_SESSION['filtro'])){
				$fechas = $this->model->fechasParaDateRangePicker();
				$_SESSION['filtro']['fechaA_desde'] = $fechas[0]['desde'];
				$_SESSION['filtro']['fechaA_hasta'] = $fechas[0]['hasta'];
			}
	
			$data = array(
				'filtroSucursales'=>$filtroSucursales,
				'filtroFamilias'=>$filtroFamilias,
				'perfil'=>$perfil,
				'acciones'=>'Omitir',
			);
			
			if($parametro=='Surticoma'){
				$this->view->render($this, 'usuarios', $data);
			} else {
				$this->view->render($this, 'proveedores', $data);
			}
		} else {
			$location = '';
			if(!empty($_SERVER['REQUEST_URI'])) $location = "?r=".urlencode($_SERVER['REQUEST_URI']);
			//header("Location: ".URL.$location."#acceder");
			header("Location: ".URL);
		}

	}
	
	function sucursales($parametro=''){
		if($_SESSION['usuario']['codigo_pro']!=0) die("No tiene acceso a esta sección");
		$perfil				= $this->model->perfil($_SESSION['usuario']['codigo_pro']);

		if( Session::exists() ) {
			$data = array(
				'perfil'=>$perfil,
				'acciones'=>'Omitir',
			);
			
			$this->view->render($this, 'sucursales', $data);
				
		} else {
			$location = '';
			if(!empty($_SERVER['REQUEST_URI'])) $location = "?r=".urlencode($_SERVER['REQUEST_URI']);
			//header("Location: ".URL.$location."#acceder");
			header("Location: ".URL);
		}

	}
	
	//20170311 asunza se agrego la pantalla de configuracion
	function configura($parametro=''){
		if($_SESSION['usuario']['codigo_pro']!=0) die("No tiene acceso a esta sección");
		$perfil				= $this->model->perfil($_SESSION['usuario']['codigo_pro']);

		if( Session::exists() ) {
			$data = array(
				'perfil'=>$perfil,
				'acciones'=>'Omitir',
			);
			
			$this->view->render($this, 'configura', $data);
				
		} else {
			$location = '';
			if(!empty($_SERVER['REQUEST_URI'])) $location = "?r=".urlencode($_SERVER['REQUEST_URI']);
			//header("Location: ".URL.$location."#acceder");
			header("Location: ".URL);
		}

	}
	
	//20170311 asunza se creo la funcio para cargar el pie de pagina
	function piepagina(){
		
		$mensaje = $cuenta = $this->model->mensajepie();
		echo $mensaje;
		}
	
	
	function log($params='login,Inicio de sesión'){
		$params = explode(',',$params);
		$datos = array(
			'codigo_pro' 	=> $_SESSION['usuario']['codigo_pro'],
			'sid' 			=> $_SESSION['usuario']['secreto'],
			'tipo'			=> $params[0],
			'accion'		=> $params[1],
		);
		
		echo $this->model->log($datos);
		//echo "<pre>";
		//print_r($datos);
		
	}

	
	//20170311 asunza funcion subir imagen.
	function cambiaImgaProv(){
			$ruta="./public/images/proveedores/";
			$texto=$_POST['texto'];
			$respuesta = "";
			foreach ($_FILES as $key) {
				
				if ($key["type"]!="image/jpeg" && $key["type"]!="image/png") {
					$respuesta = "El archivo no tiene el formato adecuado.-2";	
					}else{
							if($key['error'] == UPLOAD_ERR_OK ){//Verificamos si se subio correctamente
							if ($key["type"]=="image/jpeg"){
								$nombre = $texto.'_perfil_img.jpg';//Obtenemos el nombre del archivo
							 }
							 if ($key["type"]=="image/png"){
								$nombre = $texto.'_perfil_img.png';//Obtenemos el nombre del archivo
							 }
							$temporal = $key['tmp_name']; //Obtenemos el nombre del archivo temporal
							$tamano= ($key['size'] / 1000)."Kb"; //Obtenemos el tamaño en KB
							
						if (file_exists($ruta.$nombre)){
							    unlink($ruta.$nombre);									
								move_uploaded_file($temporal, $ruta.$nombre); //Movemos el archivo temporal a la ruta especificada				
								$cuenta = $this->model->updateImgProve($nombre,$texto);
								$respuesta = "El archivo se ha sustituido-1-".$nombre;
								
							}else{
								move_uploaded_file($temporal, $ruta.$nombre); //Movemos el archivo temporal a la ruta especificada		
								$cuenta = $this->model->updateImgProve($nombre,$texto);
								$respuesta = "imagen guardada con exito.-1-".$nombre;			
								}
								
								
							}else{
								$respuesta =  $key['error']."-2"; //Si no se cargo mostramos el error
							}
						}		
			}
			
			echo $respuesta;
	}
	
	
	//20170311 asunza funcion que carga la imagen de proveedor en el sistema
	function traerImgPro($parametro){
		$respuesta = $this->model->traerImagen($parametro);
		echo $respuesta;
		}
		
		

//20170313 asunza funcion que busca la familia de la categoria(subfamilia) seleccionada
function traeFamilia($codeSub){
	$respuesta = $this->model->familiaSubfami($codeSub);
	echo $respuesta;
	}
	
	### AUXILIARES ###
	
	function sesionMonitor(){
		print_r($_SESSION);	
	}
	
	function sesionFiltro(){
		if(isset($_SESSION['filtro']['codigo_sub'])){
			echo 1;
		} else {
			echo 0;	
		}
	}
		
}



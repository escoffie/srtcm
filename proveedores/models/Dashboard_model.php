<?php

class Dashboard_model extends Model {

	/*
	* Función constructora
	*/
	function __construct(){
		parent::__construct();
	}
	
	function perfil($codigo_pro){
		$resultado = $this->db->query("SELECT * FROM proveedores_pro WHERE codigo_pro=$codigo_pro");
		if($resultado->num_rows>0){
			$arreglo = $resultado->fetch_assoc();	
		} else {
			$arreglo = false;	
		}
		return $arreglo;
	}
	
	function perfilUpdate($datos){
		extract($datos);
		$mensaje['success']=false;
		$mensaje['mensaje']='No se ejecutó la consulta';
		if($pw_pro=='' and $pw_pro2==''){
			$password='';
			$ejecuta=true;
		}
		if($pw_pro!='' and $pw_pro2!='' and $pw_pro==$pw_pro2){
			$password = ", pw_pro=MD5('$pw_pro')";
			$ejecuta=true;
		}
		if($pw_pro!='' and $pw_pro2!='' and $pw_pro!=$pw_pro2){
			$mensaje['success']=false;
			$mensaje['mensaje']="Las contraseñas no coinciden. Intente de nuevo.";
			$ejecuta=false;	
		}
		if($ejecuta){
			$resultado = $this->db->query("UPDATE proveedores_pro SET direccion_pro='$direccion_pro', telefono_pro='$telefono_pro', email_pro='$email_pro' $password WHERE codigo_pro=$codigo_pro");
			if($resultado){
				$mensaje['success']=true;
				$mensaje['mensaje']="Actualización exitosa. Gracias.";	
			}
		}
		return $mensaje;
	}
	
	/* 
	* sucursalesPorProveedor()
	* @param $codigo_pro int
	* Devuelve una lista de los códigos de sucursal del proveedor
	*/
	function sucursalesPorProveedor($codigo_pro){
		// Si hay un código de proveedor, busca las sucursales donde ese proveedor tiene participación
		// Si no (es decir, si es cuenta MASTER), devuelve todas las sucursales
		if($codigo_pro!=0)
			$resultado = $this->db->query('CALL sucursalesPorProveedor('.$codigo_pro.');');
		else
			$resultado = $this->db->query('CALL sucursales();');
		if($resultado->num_rows>0){
			while($row = $resultado->fetch_assoc()){
				$arreglo[] = $row;
			}
		}
		$resultado->close();
		$this->db->next_result();
		return $arreglo;
	}

	/* 
	* familiasPorProveedor()
	* @param $codigo_pro int
	* Devuelve una lista de códigos de familia de productos
	*/
	function familiasPorProveedor($codigo_pro){
		// Si hay un código de proveedor, busca las familias donde ese proveedor tiene participación
		// Si no (es decir, si es cuenta MASTER), devuelve todas las familias
		if($codigo_pro!=0)
			$resultado = $this->db->query('CALL familiasPorProveedor('.$codigo_pro.');');
		else
			$resultado = $this->db->query('CALL familias();');
		if($resultado->num_rows>0){
			while($row = $resultado->fetch_assoc()){
				$arreglo[] = $row;
			}
		}
		$resultado->close();
		$this->db->next_result();
		return $arreglo;
	}
//20170313 funciom que devuelve el codigo de la familia
function familiaSubfami($codeSub){

		$resultado = $this->db->query('SELECT codigo_fam FROM subfamilias_sub where codigo_sub = "'.$codeSub.'"');
		if($resultado->num_rows>0){
				while($row = $resultado->fetch_assoc()){
					$mensaje = $row['codigo_fam'];
					}
				}else{
					$mensaje = "";
					}
					
					return $mensaje;
		return $mensaje;
	}

	/* 
	* subfamiliasPorFamiliaPorProveedor()
	* @param $codigo_fam int
	* @param $codigo_pro int
	* Devuelve una lista de códigos de subfamilia de productos
	*/
	function subfamiliasPorFamiliaPorProveedor($codigo_fam, $codigo_pro){
		// Si hay un código de proveedor, busca las subfamilias donde ese proveedor tiene participación
		// Si no (es decir, si es cuenta MASTER), devuelve todas las subfamilias de la familia pasada como parámetro
		if($codigo_pro>0)
			//2402017 asunza se cambio esta linea a peticion del ingeniero medina 
			//$resultado = $this->db->query('CALL subfamiliasPorFamiliaPorProveedor('.$codigo_fam.','.$codigo_pro.');');
			$resultado = $this->db->query('CALL SUBFAMPROVE2('.$codigo_pro.');');
		else
			$resultado = $this->db->query('CALL subfamiliasPorFamilia('.$codigo_fam.');');
		if($resultado->num_rows>0){
			while($row = $resultado->fetch_assoc()){
				$arreglo[] = $row;
			}
		}
		$resultado->close();
		$this->db->next_result();
		return $arreglo;
	}
	
	/*
	* totalesPorPeriodo()
	*/
	function totalesPorPeriodo($data){
		
		// Se preparan los array que contendrán fechas y datos para Morris
		
		$periodo_actual   = array('fechas'=>'','morris'=>'','tabla'=>'');
		$periodo_anterior = array('fechas'=>'','morris'=>'','tabla'=>'');
		
		// Se trabaja primero con el periodo actual
		
		$periodo_actual['fechas']['desde'] = $fecha_desde = $data['fechaA_desde'];
		$periodo_actual['fechas']['hasta'] = $fecha_hasta = $data['fechaA_hasta'];
		
		$query = 'CALL totalesPorPeriodo('.$_SESSION['usuario']['codigo_pro'].','.$data['codigo_sub'].',\''.$fecha_desde.'\',\''.$fecha_hasta.'\',\''.$data['where_sucursales'].'\')';
		
		$resultado = $this->db->query($query);
		$data_actual = array(array('label'=>'No se encontraron resultados con este filtro','value'=>0));
		if($resultado->num_rows>0){
			$cienporciento = 0;
			$data_actual=array();
			$i=0;
			while($row = $resultado->fetch_assoc()){
				$c = $row['codigo_pro']; 
				$p = $this->ocultarNombreParaNivelUno($c, $row['Proveedor'], '[Competidor '.$i.' periodo actual]');
				$v = floatval($row['ventas']);
				
				$c = '<span class="hidden>"'.$i.'</span><i class="fa fa-square">';
				/*17022017 se oculto a peticipon de l ing. medina
				</i> <button type="button" data-nombrepro="'.$p.'" data-codigopro="'.$c.'" data-fechaini="'.$fecha_desde.'" data-fechafin="'.$fecha_hasta.'"><i class="fa fa-th-list"> </i> Ver más</button>';*/
				$i++;
				array_push(
					$data_actual,
					array(
						'label' => $p,
						'value' => $v
					)
				);
				$cienporciento+=floatval($row['ventas']);
				$periodo_actual['tabla'][] = array($c, $p, $v, $v);
			}
			foreach($data_actual as $key => $item){
				$data_actual[$key]['value'] = round($item['value']*100/$cienporciento,2);	
			}
			foreach($periodo_actual['tabla'] as $key => $value){
				$periodo_actual['tabla'][$key][2] = '$ '.money_format($value[2], 2);
				$periodo_actual['tabla'][$key][3] = number_format(round($value[3]*100/$cienporciento,2),2,'.','').'%';
			}
		}
		
		$resultado->close();
		$this->db->next_result();

		// Se trabaja ahora con el periodo anterior
		
		$periodo_anterior['fechas']['desde'] = $fecha_desde = $data['fechaB_desde'];
		$periodo_anterior['fechas']['hasta'] = $fecha_hasta = $data['fechaB_hasta'];
		
		$query = 'CALL totalesPorPeriodo('.$_SESSION['usuario']['codigo_pro'].','.$data['codigo_sub'].',\''.$fecha_desde.'\',\''.$fecha_hasta.'\',\''.$data['where_sucursales'].'\')';
		
		$resultado = $this->db->query($query);
		$data_anterior = array(array('label'=>'No se encontraron resultados con este filtro','value'=>0));
		if($resultado->num_rows>0){
			$cienporciento = 0;
			$data_anterior=array();
			$i=0;
			while($row = $resultado->fetch_assoc()){
				$c = $row['codigo_pro'];
				$p = $this->ocultarNombreParaNivelUno($c, $row['Proveedor'], '[Competidor '.$i.' periodo anterior]');
				$v = floatval($row['ventas']);
				$c = '<span class="hidden>"'.$i.'</span><i class="fa fa-square">';
				/*17022017 se oculto a peticipon de l ing. medina
				</i> <button type="button" data-nombrepro="'.$p.'" data-codigopro="'.$c.'" data-fechaini="'.$fecha_desde.'" data-fechafin="'.$fecha_hasta.'"><i class="fa fa-th-list"> </i> Ver más</button>';*/
				$i++;
				array_push(
					$data_anterior,
					array(
						'label' => $p,
						'value' => $v
					)
				);
				$cienporciento+=floatval($row['ventas']);
				$periodo_anterior['tabla'][] = array($c, $p, $v, $v);
			}
			foreach($data_anterior as $key => $item){
				$data_anterior[$key]['value'] = round($item['value']*100/$cienporciento,2);	
			}
			foreach($periodo_anterior['tabla'] as $key => $value){
				$periodo_anterior['tabla'][$key][2] = '$ '.money_format($value[2], 2);
				$periodo_anterior['tabla'][$key][3] = number_format(round($value[2]*100/$cienporciento,2),2,'.','').'%';
			}
		}
		$resultado->close();
		$this->db->next_result();
				
		// Prepara la salida de datos para el Controlador
		$periodo_actual['morris']   = $data_actual;
		$periodo_anterior['morris'] = $data_anterior;
		
		$arreglo['actual'] = $periodo_actual;
		$arreglo['anterior'] = $periodo_anterior;
		
		return ($arreglo);

	}
	
	
	//20170311 asunza se agrego la funcio para el mensaje del pie de pagina
	function mensajepie(){
			$mensaje = "";
			$resultado = $this->db->query("select * from piepagina_pie");
			if($resultado->num_rows>0){
				$row = $resultado->fetch_assoc();
					$mensaje = $row['mensaje'];
			
				}else{
					$mensaje = "";
					}
					
					return $mensaje;
		}
	
	
	//20170311 asunza guarda el nombre de la imagen del proveedor
	function updateImgProve($nombre,$texto){
			$this->db->query("UPDATE proveedores_pro SET avatar_pro = '".$nombre."' WHERE codigo_pro='".$texto."'");
		}
		
	//20170311 asunza obtiene la imagen del proveedor
	function traerImagen($parametro){
			$resultado = $this->db->query("SELECT avatar_pro FROM proveedores_pro where codigo_pro = '".$parametro."';");
			if($resultado->num_rows>0){
				$row = $resultado->fetch_assoc();
					$mensaje = $row['avatar_pro'];

				}else{
					$mensaje = "";
					}
					
					return $mensaje;
		}
	
	/*
	* fechasParaDateRangePicker()
	* Obtiene el intervalo de fechas de la última semana en la que se encuentren datos
	*/
	function fechasParaDateRangePicker(){
		$resultado = $this->db->query('CALL fechaDesdeHastaDefault();');
		if($resultado->num_rows>0){
			while($row = $resultado->fetch_assoc()){
				$arreglo[] = $row;
			}
		}
		$resultado->close();
		$this->db->next_result();
		return $arreglo;
	}
	
	/* 
	* ventaPorDia()
	* @param ...
	* @return array
	* Devuelve la lista del total de ventas por artículo por día
	*/
	function ventaPorDia($codigo_pro, $codigo_fam, $codigo_sub, $fecha_ini, $fecha_fin, $sucursales){
		$resultado = $this->db->query("CALL ventaPorDia($codigo_pro, $codigo_fam, $codigo_sub, '$fecha_ini', '$fecha_fin', '$sucursales')");
		if($resultado->num_rows>0){
			while($row = $resultado->fetch_assoc()){
				$arreglo[] = $row;
			}
		} else {
			$arreglo[] = array("status"=>"Error", "Error"=> "No hay datos");	
		}
		$resultado->close();
		$this->db->next_result();
		return $arreglo;
	}
	
	/*
	* ticketsPorArticuloPorDia()
	*/
	function ticketsPorArticuloPorDia($codigo_art, $fecha, $sucursales){
		$resultado = $this->db->query("CALL ticketsPorArticuloPorDia($codigo_art, '$fecha', '$sucursales')");
		if($resultado->num_rows>0){
			while($row = $resultado->fetch_assoc()){
				$arreglo[] = $row;
			}
		} else {
			$arreglo[] = array("status"=>"Error", "Error"=> "No hay datos");	
		}
		$resultado->close();
		$this->db->next_result();
		return $arreglo;
	}
	
	/* 
	* histograma
	*/
	function histograma($fecha_ini, $fecha_fin, $sucursales, $codigo_sub, $codigo_pro){
		
		$resultado1 = $this->db->query("CALL proveedoresPorFechaPorSubfamilia('$fecha_ini', '$fecha_fin', '$sucursales', $codigo_sub, $codigo_pro)");
		$proveedores=array();
		if($resultado1->num_rows>0){
			while($row1 = $resultado1->fetch_assoc()){
				$proveedores[] = $row1;
			}
		}
		
		$resultado1->close();
		$this->db->next_result();
		
		$i=0;
		$arreglo=array();
		if(sizeof($proveedores)>0){
			foreach($proveedores as $proveedor){
				$arreglo['theader'][$proveedor['codigo_pro']] = $this->ocultarNombreParaNivelUno($proveedor['codigo_pro'], $proveedor['proveedor'], "[Comp $i]");
				$i++;
	
				// Loop rango de fechas
				$fecha = $fecha_ini;
				while (strtotime($fecha) <= strtotime($fecha_fin)) {
				  $arreglo[$fecha][$proveedor['codigo_pro']] = 0.00; 
				  $fecha = date ("Y-m-d", strtotime("+1 day", strtotime($fecha)));
				}
			
				
				$montos = $this->db->query("CALL montosPorDiaPorProveedorPorSubfamilia('$fecha_ini', '$fecha_fin', $proveedor[codigo_pro], $proveedor[codigo_fam], $proveedor[codigo_sub], '$sucursales')");
				
				if($montos->num_rows>0){
					$suma=0;
					while($monto = $montos->fetch_Assoc()){
						$arreglo[$monto['fecha']][$proveedor['codigo_pro']] = (float)$monto['monto'];
						$suma += $monto['monto'];
					}
				}
				
				$arreglo['tfooter'][$proveedor['codigo_pro']] = $suma;
				
				$montos->close();
				$this->db->next_result();
				
			}
		}
		
		return array_reverse($arreglo);
		
	}
	
	function compararTotalesPorPeriodo($fechaA_ini, $fechaA_fin, $fechaB_ini, $fechaB_fin, $codigo_fam, $codigo_sub, $codigo_pro, $sucursales){
		$resultado = $this->db->query("CALL compararTotalesPorPeriodo('$fechaA_ini', '$fechaA_fin', '$fechaB_ini', '$fechaB_fin', $codigo_fam, $codigo_sub, $codigo_pro, '$sucursales')");
		if($resultado->num_rows>0){
			$i=0;
			while($row = $resultado->fetch_assoc()){
				if((float)($row['suma_actual'])>0 and (float)($row['suma_anterior'])>0)
				$diferencia = ((float)($row['suma_actual'] - (float)$row['suma_anterior']))/(float)$row['suma_anterior']*100;//17022017 asunza se agrego por peticion del ingeniero medina
				else if((float)($row['suma_anterior'])==0) $diferencia = 100;//17022017 asunza se agrego por peticion del ingeniero medina
				else if((float)($row['suma_actual'])==0) $diferencia = -100;//17022017 asunza se agrego por peticion del ingeniero medina
				$row['variacion'] = round($diferencia,2);//17022017 asunza se agrego por peticion del ingeniero medina
				$row['suma_actual'] = (float)$row['suma_actual'];
				$row['suma_anterior'] = (float)$row['suma_anterior'];
				$x = explode(' ',$row['proveedor']);
				$row['proveedor'] = $this->ocultarNombreParaNivelUno($row['codigo_pro'], $x[0].' '.$x[1].' '.$x[2], "[Competidor $i]");
				$arreglo[] = $row;
				$i++;
			}
		}
		return $arreglo;
	}

	function comparativoDropdown(){
		extract($_SESSION['filtro']);
		$codigo_pro = $_SESSION['usuario']['codigo_pro'];
		$resultado = $this->db->query("CALL proveedoresPorFechaPorSubfamilia('$fechaA_desde', '$fechaA_hasta', '$where_sucursales', $codigo_sub, $codigo_pro)");
		$proveedores = array();
		if($resultado->num_rows>0){
			while($row = $resultado->fetch_assoc()){
				$proveedores[] = $row;
			}
		}
		
		return $proveedores;
		
		$resultado->close();
		$this->db->next_result();
	}
	
	function comparativoDataMorris($codigo_pro=''){
		extract($_SESSION['filtro']);
		$resultado = $this->db->query("CALL productosPorProveedorDos($codigo_fam, $codigo_sub, $codigo_pro, '$fechaA_desde', '$fechaA_hasta', '$where_sucursales')");
		//$resultado = $this->db->query("CALL productosPorProveedor($codigo_fam, $codigo_sub, $codigo_pro, '$fechaA_desde', '$fechaA_hasta', '$where_sucursales')");


		if($resultado->num_rows>0){
				
			$i=1;
			while($row = $resultado->fetch_assoc()){
				$productos[] = array(
					'label' => $this->ocultarNombreParaNivelUno($codigo_pro, $row['articulo'], "[Artículo $i]"). ' ' .$row['capacidad']. ' ' .$row['unidad'],
					'value' => $row['porcentaje'],
				);
				$i++;
				
			}
		}
		
		return $productos;
		
		$resultado->close();
		$this->db->next_result();
	}
	
	function comparativoDataTable($codigo_pro=''){
		extract($_SESSION['filtro']);
		if(empty($codigo_pro)) $codigo_pro=0;
		$resultado = $this->db->query("CALL productosPorProveedorDos($codigo_fam, $codigo_sub, $codigo_pro, '$fechaA_desde', '$fechaA_hasta', '$where_sucursales')");
		//$resultado = $this->db->query("CALL productosPorProveedorPorFecha($codigo_fam, $codigo_sub, $codigo_pro, '$fechaA_desde', '$fechaA_hasta', '$where_sucursales')");
		if($resultado->num_rows>0){
			$i=0;
			/*while($row = $resultado->fetch_assoc()){
				$productos[$i] = array(
					date('d/m/Y', strtotime($row['fecha'])),
					$this->ocultarNombreParaNivelUno($codigo_pro, $row['codigodebarra'], "[Código de barra $i]"),
					$this->ocultarNombreParaNivelUno($codigo_pro, $row['articulo'], "[Artículo $i]"),
					$row['capacidad'].' '.$row['unidad'],
					$row['cantidad'],
					$this->moneda($row['monto']),
				);*/
				
				while($row = $resultado->fetch_assoc()){
				$productos[$i] = array(
					$this->ocultarNombreParaNivelUno($codigo_pro, $row['codigodebarra'], "[Código de barra $i]"),
					$this->ocultarNombreParaNivelUno($codigo_pro, $row['articulo'], "[Artículo $i]"),
					$row['capacidad'].' '.$row['unidad'],
					$row['cantidad'],
					$this->moneda($row['monto']),
				);
				$i++;
			}
		}
		
		return $productos;
		
		$resultado->close();
		$this->db->next_result();
	}
	
	function monitorFiltro(){
		$sucursales = $this->db->query("SELECT CONCAT(region_suc,' - ',nombre_suc) AS nombre_suc FROM sucursales_suc WHERE FIND_IN_SET(codigo_sucursal_suc, '".$_SESSION['filtro']['where_sucursales']."')");
		if($sucursales->num_rows>0){
			while($row = $sucursales->fetch_assoc()){
				$respuesta['sucursales'][]=$row['nombre_suc'];
			}
			$respuesta['sucursales']=implode(', ', $respuesta['sucursales']);
		}

		$fam = $this->db->query("SELECT a.nombre_fam AS familia, b.nombre_sub AS subfamilia FROM familias_fam AS a JOIN subfamilias_sub AS b USING(codigo_fam) WHERE b.codigo_fam=".$_SESSION['filtro']['codigo_fam']." AND b.codigo_sub=".$_SESSION['filtro']['codigo_sub']);
		if($fam->num_rows>0){
			while($row = $fam->fetch_assoc()){
				$respuesta['familia']=$row['familia'];
				$respuesta['subfamilia']=$row['subfamilia'];
			}
		}
		
		$respuesta['fechas'] = 'De '.date("d/m/Y", strtotime($_SESSION['filtro']['fechaA_desde'])). ' a '.date("d/m/Y", strtotime($_SESSION['filtro']['fechaA_hasta']));
		
		return $respuesta;
	}
	
	function excel(){
		$consulta="CALL exportar(
		'".$_SESSION['filtro']['fechaA_desde']."',
		'".$_SESSION['filtro']['fechaA_hasta']."',
		'".$_SESSION['filtro']['codigo_fam']."',
		'".$_SESSION['filtro']['codigo_sub']."',
		'".$_SESSION['filtro']['where_sucursales']."'
		)";
		$resultado = $this->db->query($consulta);
		if($resultado->num_rows>0){
			while($row = $resultado->fetch_assoc()){
				$row['Compañía'] = $this->ocultarNombreParaNivelUno($row['codigo_pro'], $row['Compañía'], "[Compañía]");
				$row['Artículo'] = $this->ocultarNombreParaNivelUno($row['codigo_pro'], $row['Artículo'], "[Artículo]");
				$row['Código de barra'] = $this->ocultarNombreParaNivelUno($row['codigo_pro'], $row['Código de barra'], "[Código de barra]");
				$arreglo[] = $row;
			}
		}
		$resultado->close();
		$this->db->next_result();
			$q = "INSERT INTO log (codigo_pro, sid, timestamp, tipo, accion) VALUES (".$_SESSION['usuario']['codigo_pro'].", '".$_SESSION['usuario']['secreto']."', '".date('Y-m-d H:i:s')."', 'Descarga', 'Exportar a Excel')";
			$r = $this->db->query($q);
		return $arreglo;
	}
	
	function log($datos){
		extract($datos);
		if($codigo_pro>0){
			$q = "INSERT INTO log (codigo_pro, sid, timestamp, tipo, accion) VALUES ($codigo_pro, '$sid', '".date('Y-m-d H:i:s')."', '$tipo', '$accion')";
			$r = $this->db->query($q);
			if($r) return 1; else return 0;	
		}
	}
	
	/*
	===================================================
	FUNCIONES AUXILIARES GENERALES PARA DASHBOARD_MODEL
	===================================================
	*/
	
	public function ocultarNombreParaNivelUno($codigo_pro, $dato, $etiqueta='[......]'){
		$id_niv = $_SESSION['usuario']['id_niv'];
		if($codigo_pro!=$_SESSION['usuario']['codigo_pro'] and $id_niv==1){
			return $etiqueta;
		} else {
			return $dato;
		}
		
	}
	
	public function elipsis($in, $length=10){
		$out = strlen($in) > $length ? substr($in,0,$length)."..." : $in;
		return $out;
	}
	
	public function moneda($numero){
		return "$ ". number_format($numero, 2, '.', ',');
	}
	

}

// 17022017 asunza
// se agrego esta funcion ya que en el ambiente test en una maquina windows no se puede acceder ala funcion money_format, el cual es una funcion de sistema operativo el cual linux si tiene. 
if (!function_exists('money_format')) {
	function money_format($numero,$i){
		return number_format($numero,$i,'.',',');
		}
}
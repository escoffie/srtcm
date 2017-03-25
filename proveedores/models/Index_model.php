<?php

class Index_model extends Model {

	function __construct(){
		parent::__construct();
	}

	//Inserta datos del formulario de contacto.
	function enviarMensaje($data){
		return $this->db->insert('mensajes_msg', $data);
	}

	//Regresa los datos de un mensaje desde la base de datos
	function selectMensaje($id_msg){
		return $this->db->select('*', 'mensajes_msg', 'id_msg='.$id_msg);
	}

	//Efectúa el cálculo del ahorro
	function calcularAhorro($data){
		
		//$fpc = $data['frecuenciapago_cal'];
		//$monto = $data['monto_cal'];

		$fpc = 6;
		$monto = $data['monto_cal'] * $data['frecuenciapago_cal'];

		$tarifa_cal = $this->db->select('tarifa_es', 'estados_es', 'id_es='.$data['id_es']);
		$tarifa_cal = $tarifa_cal[0]['tarifa_es'];

		$variables = $this->db->select('variable_cal, valor_cal', 'calculadora_cal', 'tarifa_cal="fijo" OR tarifa_cal="'.$tarifa_cal.'"');

		//Arreglo de variables y valores para cálculo, usando el estado como filtro de tarifa
		foreach ($variables as $key => $value) {
			$calculo[$value['variable_cal']] = $value['valor_cal'];
		}

		//print_r($calculo);

		//Pasa de array a variables,usando el key como nombre de variable
		extract($calculo);

		//Se procede a hacer los pasos del cálculo

		//Primero se determina el umbral
		$umbral = (((($limiteanual + 1)*$preciodac)/$fpc)*(1+$iva))+$cargofijo;

		//echo "Pago > $".round($umbral,2);

		//Si el monto ingresado por el usuario es mayor que el umbral
		if($monto > $umbral){
			$kwhanual = ((($monto/(1+$iva))-$cargofijo)/$preciodac)*$fpc;
			$paneles  = (($kwhanual-($limiteanual-($limiteanual-$limiteanualexcedentes)))/$papp);
		} else {
			$kwhanual = ((((($monto/(1+$iva))-$dap)-$cargoxkwsub)/$excedentes)+($limiteanualexcedentes/$fpc))*$fpc;
			$paneles  = ($kwhanual-$limiteanualexcedentes)/$papp;
		}

		$produccionkwhanual = $paneles * $papp;
		$nuevopagoanual = ((($kwhanual-$produccionkwhanual)*$promediosub)*(1+$iva))+($dap*$fpc);
		$consumoanual = $monto*$fpc;
		$ahorroanual = $consumoanual-$nuevopagoanual;

		/*$resultado = array(
			'produccion' => round($produccionkwhanual),
			'consumo' => round($kwhanual),
			'ahorro' => round($ahorroanual),
			);*/
			
		$resultado = array(
			'produccion' => round($nuevopagoanual),
			'consumo' => round($consumoanual),
			'ahorro' => round($ahorroanual),
			);

		echo json_encode($resultado);

		/*echo "\n\rkwh anual: ". round($kwhanual);
		echo "\n\rNúmero de paneles: ". round($paneles);
		echo "\n\rProducción anual: ". round($produccionkwhanual);
		echo "\n\rNuevo pago anual: ". round($nuevopagoanual,2);
		echo "\n\rConsumo anual: ". round($consumoanual,2);
		echo "\n\rAhorro anual: ". round($ahorroanual,2);*/

	}

}
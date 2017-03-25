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
	

//20170310 asunza: se agrego la funcion para validar el correo en la vista recordar contraseña.	
	function validaCorreo($correo){
			$mensaje = "";
			$resultado = $this->db->query("SELECT * FROM proveedores_pro WHERE email_pro='".$correo."' AND estatus_pro>0");
			if($resultado->num_rows>0){
			try{
					$arreglo = 1;
				
					$row = $resultado->fetch_assoc();
					$x = explode(' ',$row['razonsocial_pro']);
					$new_contrasenia = $x[0].rand(5,15);
					$new_contrasenia_encriptada = md5($new_contrasenia);
					
				$resultado2 = $this->db->query("UPDATE proveedores_pro SET pw_pro = '".$new_contrasenia_encriptada."' WHERE email_pro='".$correo."'");
				 
				 $mensaje = "<h1>Restablecimiento de Contraseña.</h1>";
				 $mensaje .= "<p>Su contraseña es: </p>";
				 $mensaje .= "<p style=\"color:green;\">$new_contrasenia</p>";
				 $mensaje .= "<p style=\"color:red;\">Por seguridad se recomienda cambiar su contraseña a la brevedad posible.</p>";
				  $mensaje .= "<p style=\"color:blue;\">¿Necesita ayuda?  (999) 611 8100 ext. 241 de lunes a viernes, de 9:00 a 17:00 y sábados de 9:00 a 14:00.</p>";
						
					//PHPMailer
					$mail = new PHPMailer;
					$mail->CharSet = 'UTF-8';
					
					//$mail->SMTPDebug = 3;
					$mail->isSMTP();
					$mail->Host = SERVIDORSALIDA;
					$mail->SMTPAuth = true;
					$mail->Username = USUARIO;
					$mail->Password = PASSWORD;
					if(PUERTOSALIDA==587){
						$mail->SMTPSecure = "tls";	
					}
					$mail->Port = PUERTOSALIDA;
					$mail->SMTPOptions = array(
							'ssl' => array(
								'verify_peer' => false,
								'verify_peer_name' => false,
								'allow_self_signed' => true
							)
						);
					$mail->From = REMITENTE;
					$mail->FromName = REMITENTENOMBRE;
					$mail->addAddress("$correo");
					//$mail->addAddress("reporte_crmproveedores@coma.com.mx");
					$mail->isHTML(true);
					$mail->Subject = "CRM Surticoma - Envío de Contraseña " . date('Y-m-d H:i:S');
					$mail->Body = "$mensaje";
			
					if(!$mail->send()){
					$error = "Hubo un error: " . $mail->ErrorInfo;
						
					/*$mail->From = REMITENTE;
					$mail->FromName = REMITENTENOMBRE;
					$mail->addAddress("asunza@coma.com.mx");
					//$mail->addAddress("reporte_crmproveedores@coma.com.mx");
					$mail->isHTML(true);
					$mail->Subject = "CRM Surticoma Error - Envío de Contraseña " . date('Y-m-d H:i:S');
					$mail->Body = "$error";*/
					
					
					
						}/*else{
							$arreglo = "correo enviado";
							}*/
			}catch(Exception $e){
					$arreglo = 2;
				}
				
					
				
				
			} else {
				$arreglo = 2;	
			}
			return $arreglo;
		}

}
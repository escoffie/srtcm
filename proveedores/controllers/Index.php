<?php
//CONTROLADOR Index.php

class Index extends Controller {
	
	function __construct(){
		parent::__construct();
	}
	
	function index(){
		$this->view->render($this, 'index');
	}
	
	function privacidad(){
		$this->view->render($this, 'privacidad');
	}
	
	function pruebaMail(){
		$mensaje = file_get_contents(URL."email-templates/landing-contacto-confirmacion.php");
		$remplazos = array(
				'({nombre_msg})'=>'Bernardo',
				'({correo_msg})'=>'bernardoescoffie@gmail.com',
				'({telefono_msg})'=>'999 999 9999',
				'({mensaje_msg})'=>'<h1>Mensaje de prueba</h1><p>Esto es una prueba.</p>',
			);
		$mensaje = preg_replace( array_keys( $remplazos ), array_values( $remplazos ), $mensaje );
		
		//PHPMailer
		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';
		
		$mail->SMTPDebug = 3;
		$mail->isSMTP();
		$mail->Host = 'smtpout.secureserver.net';
		$mail->SMTPAuth = true;
		$mail->Username = 'ventas@ecopulse.mx';
		$mail->Password = 'ventasEcopulse';
		$mail->Port = 80;
		if(PUERTOSALIDA==587){
			$mail->SMTPSecure = "tls";	
		}
		
		$mail->From = REMITENTE;
		$mail->FromName = REMITENTENOMBRE;
		$mail->addAddress('bernardoescoffie@gmail.com');
		$mail->addBCC(PIPEDRIVEMAIL);
		$mail->isHTML(true);
		$mail->Subject = "Gracias, ".'Bernardo';
		$mail->Body = $mensaje;
		
		if(!$mail->send()){
			echo "<p>Error en Index/pruebaMail: \n\r Correo no se pudo enviar</p>";
		} else {
			echo '<p>Éxito</p>';
			//header('Location:gracias.php?u='.$correo['nombre_us']);
		}
		$mail->ErrorInfo;
	}
	
	function enviarMensaje(){
		//Si están llenos los datos:
		if(
			!empty($_POST['nombre_msg']) and 
			!empty($_POST['correo_msg']) and 
			!empty($_POST['telefono_msg']) and 
			!empty($_POST['mensaje_msg'])  
		){
			
			$_POST['nombre_msg'] = filter_var($_POST['nombre_msg'], FILTER_SANITIZE_STRING);
			$_POST['correo_msg'] = filter_var($_POST['correo_msg'], FILTER_SANITIZE_EMAIL);
			$_POST['telefono_msg'] = filter_var($_POST['telefono_msg'], FILTER_SANITIZE_STRING);
			$_POST['mensaje_msg'] = filter_var($_POST['mensaje_msg'], FILTER_SANITIZE_STRING);

			$sepuedeenviar = false;
			
			//Pipedrive
			$pd = new PipedriveAPI();
			
			$person['name'] = $_POST['nombre_msg'];
			$person['email'] = $_POST['correo_msg'];
			$person['phone'] = $_POST['telefono_msg'];

			$person_result = $pd->addPerson($person);
			
			if($person_result['success']==false){
				
				echo "<p>Error creando persona al enviar mensaje</p>";	
				var_dump($person_result);

			} else {
				
				$person_id = $person_result['data']['id'];

				$deal['title'] = "Contactado por ".$_POST['nombre_msg'];
				$deal['person_id'] = $person_id;
				$deal['lead'] = 'Sitio web: formulario de contacto';
				$deal['stage_id'] = $pd->model->getStageByPipelineAndStep(1);
				
				$deal_result = $pd->addDeal($deal);

				if($deal_result['success']==false){
					echo "<p>Error creando Deal</p>";
					var_dump($deal_result);
				
				} else {

					$deal_id = $deal_result['data']['id'];
					
					$note['content'] = $_POST['mensaje_msg'];
					$note['deal_id'] = $deal_id;

					$note_result = $pd->addNote($note);

					if($note_result['success']==false){
						echo "<p>Error creando Note</p>";
						var_dump($note_result);
						$sepuedeenviar = false;
					} else {
						$sepuedeenviar = true;
					}

					$activity['deal_id'] = $deal_id;
					$activity['type'] = 'email';
					$activity['subject'] = "Escribir de vuelta a ".$_POST['nombre_msg'];
					
					$activity_result = $pd->addActivity($activity);

					if($activity_result['success']==false){
						echo "Error al crear actividad al enviar mensaje. ";
						var_dump($activity_result);
						$sepuedeenviar = false;
					} else {
						$sepuedeenviar = true;
					}
				}
			}
			
			if($sepuedeenviar == true) {
			
				//Prepara los datos que se enviarán
				$dtmx = new DateTime('now', new DateTimeZone(TZ_CDMX));

				$data['nombre_msg'] = $_POST['nombre_msg'];
				$data['correo_msg'] = $_POST['correo_msg'];
				$data['telefono_msg'] = $_POST['telefono_msg'];
				$data['mensaje_msg'] = $_POST['mensaje_msg'];
				$data['fecha_msg'] = $dtmx->format('Y-m-d H:i:s');
				$data['ip_msg'] = $_SERVER['REMOTE_ADDR'];
				$data['estatus_msg'] = 0;
				
				//Manda al modelo para a) ingresar a la base de datos y b) enviar un correo
				
				$response = $this->model->enviarMensaje($data);
				
				if($response['success']==true){
					$enviado = $this->model->selectMensaje($response['insert_id']);
					$enviado = $enviado[0];
					//Si se mandó el mail, redirige a página de "gracias"
					$mensaje = file_get_contents(URL."email-templates/landing-contacto-confirmacion.php");
					$remplazos = array(
							'({nombre_msg})'=>$enviado['nombre_msg'],
							'({correo_msg})'=>$enviado['correo_msg'],
							'({telefono_msg})'=>$enviado['telefono_msg'],
							'({mensaje_msg})'=>$enviado['mensaje_msg'],
						);
					$mensaje = preg_replace( array_keys( $remplazos ), array_values( $remplazos ), $mensaje );
					
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
					
					$mail->From = REMITENTE;
					$mail->FromName = REMITENTENOMBRE;
					$mail->addAddress($enviado['correo_msg']);
					$mail->addBCC(PIPEDRIVEMAIL);
					$mail->isHTML(true);
					$mail->Subject = "Gracias, ".$enviado['nombre_msg'];
					$mail->Body = $mensaje;
					
					if(!$mail->send()){
						echo "Error en Index/enviarMensaje: \n\r Correo no se pudo enviar";
					} else {
						echo $response['success'];
						//header('Location:gracias.php?u='.$correo['nombre_us']);
					}
					
				} else {
					echo "Error en Index/enviarMensaje: \n\r".$response['mensaje'];
				}
			} else {
				echo "<p>No se guardó en la DB y no se envió el mensaje por error en PD</p>";
				var_dump($pd);
			}
		} else {
			echo "¡Ups! Verifica que hayas llenado todos los campos";
		}
	}
	
	function calcularAhorro(){
		if(
			isset($_POST['frecuenciapago_cal']) and 
			isset($_POST['id_es']) and 
			isset($_POST['monto_cal']) 
			) {
			$data['frecuenciapago_cal'] = $_POST['frecuenciapago_cal'];
			$data['id_es'] = $_POST['id_es'];
			$data['monto_cal'] = $_POST['monto_cal'];
			$response = $this->model->calcularAhorro($data);
		} else {
			echo "No se recibieron los datos";
		}
	}
	
	function destroySession(){
		Session::destroy();
		header("Location: ".URL);
	}
	
}

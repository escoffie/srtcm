<?php
include_once './libs/PipedriveAPI.php'; 

class User extends PipedriveAPI {

	function __construct(){
		parent::__construct();
	}

	/**
	* Crea cuenta nueva.
	* @return Mixed
	*/
	public function signUp(){
		
		//Datos de registro de la tabla usuarios_us
		if( !empty($_POST['nombre_us']) and !empty($_POST['telefono_us']) and !empty($_POST['correo_us']) and !empty($_POST['contrasena_us']) ){
			
			//Validación mínima de seguridad
			$_POST['nombre_us'] = filter_var($_POST['nombre_us'], FILTER_SANITIZE_STRING);
			$_POST['telefono_us'] = filter_var($_POST['telefono_us'], FILTER_SANITIZE_STRING);
			$_POST['correo_us'] = filter_var($_POST['correo_us'], FILTER_SANITIZE_EMAIL);
			$_POST['contrasena_us'] = filter_var($_POST['contrasena_us'], FILTER_SANITIZE_STRING);

			//Verifica si la cuenta de correo electrónico está disponible
			$correo_return = $this->model->selectUserByEmail($_POST['correo_us'], false);
			if($correo_return){
				$correo_us = $correo_return[0]['correo_us'];
				echo "¡Ups! La cuenta $correo_us ya existe.";
			} else {

				//Prepara los datos que se enviarán al modelo (acción signUp)
				$dtmex = new DateTime('now', new DateTimeZone(TZ_CDMX));

				$data['organizacion_us'] = $_POST['organizacion_us']; 
				$data['nombre_us'] = $_POST['nombre_us']; 
				$data['telefono_us'] = $_POST['telefono_us']; 
				$data['correo_us'] = $_POST['correo_us']; 
				$data['contrasena_us'] = md5($_POST['contrasena_us']);
				$data['fecha_us'] = $dtmex->format('Y-m-d');
				$data['hora_us'] = $dtmex->format('H:i:s');
				
				$response = $this->model->signUp($data); 

				if($response['success']==true){

					$correo = $this->model->selectUser($response['insert_id']);
					$correo = $correo[0];
					
					//Si se mandó el mail, redirige a página de "gracias"
					$mensaje = file_get_contents(URL."email-templates/paso0-cuentanueva.php");
					$remplazos = array(
							'({nombre_us})'=>$correo['nombre_us'],
							'({destino})'=>URL."User/activar/".md5($correo['id_us']),
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
					$mail->addAddress($correo['correo_us']);
					$mail->addBCC(PIPEDRIVEMAIL);
					$mail->isHTML(true);
					$mail->Subject = "Has dado tu primer paso para comenzar a ahorrar electricidad, ".$correo['nombre_us'];
					$mail->Body = $mensaje;
					
					if(!$mail->send()){
						echo "Error en USer/signUp: \n\r Correo no se pudo enviar";
					} else {
						echo $response['success'];
					}
				
				} else {
					echo "Error en User/signUp: \n\r".$response['mensaje'];
				}
			}

		} else {
			echo "Error en User/signUp: \n\r Datos incompletos";
		}

	}	

	/**
	* activar
	* @param int $md5_id_us (viene encriptado en MD5)
	* @return void (header location)
	*/ 
	public function activar($md5_id_us){
			
		//El parámetro que se recibe YA está en md5
		//Selecciona al usuario que se activará
		$usuario = $this->model->selectUser($md5_id_us, true);
		$usuario = $usuario[0];

		//Sólo si no está activo ya
		if($usuario['estado_us']<1) {
		
			//$param debe ser md5_id_us
			$data = array('estado_us'=> 1);
			$updateUser = $this->model->updateUser($data, $md5_id_us, true);

			//Arreglo para addPerson
			$person['name'] 	= $usuario['nombre_us'];
			$person['email'] 	= $usuario['correo_us'];
			$person['phone'] 	= $usuario['telefono_us'];
			
			//Arreglo para addDeal (todavía no es un proyecto, es un prospecto)
			if(empty($usuario['organizacion_us'])) {
				$deal['title'] = "Prospecto ".$usuario['nombre_us'];
			} else {
				$deal['title'] = "Prospecto ".$usuario['organizacion_us'];
			}
			$deal['lead'] = 'Sitio web: Prospecto nuevo';

			//Crea person, deal, note y activity, y el resultado va a un array
			$pipedrive_response = $this->activateAccountDeal($person, $deal);

			//Prepara los datos que se actualizarán en la base de datos local
			if($pipedrive_response['success']==false){
				echo "Error PD activateAccountDeal";
			} else {
				$dtmex = new DateTime('now', new DateTimeZone(TZ_CDMX));

				$primerDeal = array(
					'id_us' 		=> $usuario['id_us'],
					'id_pr'			=> null,
					'person_id'		=> $pipedrive_response['person_id'],
					'deal_id'		=> $pipedrive_response['deal_id'],
					'activity_id'	=> $pipedrive_response['activity_id'],
					'stage_id'		=> $pipedrive_response['stage_id'],
				);

				//Almacena los datos del Deal en pipedrive_pd y devuelve un array (preguntar por el key booleano 'success') 
				
				$primerDealRespuesta = $this->createPipedrive($primerDeal);
				$this->view->render($this,'gracias');
				/*
					echo "<script>";
					print_r($usuario);
					print_r($updateUser);
					print_r($person);
					print_r($pipedrive_response);
					print_r($primerDeal);
					echo "console.log(".json_encode($primerDealRespuesta).")";
					echo "</script>";

				if($primerDealRespuesta['success']==false){
					echo $primerDealRespuesta['mensaje'];	
				} else {
					echo "<pre>";
					print_r($usuario);
					print_r($updateUser);
					print_r($person);
					print_r($pipedrive_response);
					print_r($primerDeal);
					print_r($primerDealRespuesta);
					echo "</pre>";
					//					
				}*/
				
			}

		} else {
			header('Location: '.URL.'#acceder');
		}

	}

	/**
	* Inicia sesión.
	* @param Int $usar_url (default 0)
	* @return Mixed
	*/
	public function signIn($usar_url=0){

		//Datos de registro de la tabla usuarios_us
		if( !empty($_POST['codigo_pro']) and !empty($_POST['pw_pro']) )
		{

			//Prepara los datos que se enviarán al modelo (acción signUp)
			$data['codigo_pro'] = $_POST['codigo_pro']; 
			$data['pw_pro'] = md5($_POST['pw_pro']);

			//No es return, sino echo, porque se pasará el valor por AJAX
			//Imprime 1 o {error} según le diga el método insert() en el modelo
			$response = $this->model->signIn($data);
			$response = $response[0];

			if($response['codigo_pro']==$data['codigo_pro']){
				
				$this->createSession($response);

				if($usar_url==1) {
					//Cuando se accede por URL
					if(!empty($_POST['r'])) $r=ltrim($_POST['r'],'/'); else $r='Dashboard';
					header('Location: '.URL.$r);
					//echo "Hola, gracias por acceder a Ecopulse ;)";
				} else {
					//Cuando se accede por Javascript (se traduce como true)
					echo 1;
				}
				
			} else {
				echo "¡Ups! Usuario o contraseña incorrectos. Intenta de nuevo";
			}

		} else {
			echo "¡Ups! Parece que dejaste algún campo vacío. Intenta de nuevo";
		}

	}

	/**
	* Actualiza perfil del usuario y también datos de Person en Pipedrive.
	* @return void (header location)
	*/
	public function update(){
		if(isset($_POST['id_us'])){
			$id_us = $_POST['id_us'];
			$data['nombre_us'] = $_POST['nombre_us'];
			// De momento, correo no se actualiza por que es el login 
			//$data['correo_us'] = $_POST['correo_us'];
			$data['telefono_us'] = $_POST['telefono_us'];
			$data['direccion_us'] = $_POST['direccion_us'];
			//Si hay contraseña y las contraseñas coinciden...
			if($_POST['contrasena_us']!='' and $_POST['contrasena_us']==$_POST['contrasena_us_prueba']){
				$data['contrasena_us'] = md5($_POST['contrasena_us']);
			}
			//Actualiza el registro en la tabla usuarios_us
			$actualizar = $this->model->updateUser($data, $id_us);
			
			//PIPEDRIVE
			//PENDIENTE: subir (y acceder a) foto de perfil a Pipedrive
			$pipedrive_ids = $this->selectPipedrive($id_us);
			$pipedrive_ids = $pipedrive_ids[0];
			
			$person_id 		= $pipedrive_ids['person_id'];
			$deal_id 		= $pipedrive_ids['deal_id'];
			$activity_id 	= $pipedrive_ids['activity_id'];
			
			$person['direccion'] = $_POST['direccion_us'];
			
			$person['name'] = $_POST['nombre_us'];
			$person['email'] = $_POST['correo_us'];
			$person['phone'] = $_POST['telefono_us'];
			
			$person_update = $this->updatePerson($person_id, $person);
			
			$dtmex = new DateTime('now', new DateTimeZone(TZ_CDMX));
			$date = $dtmex->format('Y-m-d H:i:s');

			$note['deal_id'] = $deal_id;
			$note['content'] = 'Información de la persona actualizada en '.$date;
			$note_add = $this->addNote($note);
			
			if(!$person_update){
				echo "Error User/update: Person";
			} else if(!$note_add['success']) {
				echo "Error User/update: Note: ".$note_add['error'];
				print_r($note_add);
			}else {

				$response = $this->model->selectUser($id_us);
				$response = $response[0];

				if($response['correo_us']==$_POST['correo_us']){
					$this->createSession($response);
					header("Location: ".URL.$_POST['regreso']);
				} else {
					print_r($response);
				}
			}
			
		}
	
	}

	//Crear sesión
	function createSession($data){
		foreach ($data as $key => $value) {
			Session::setValue($key, $value);
		}
	}

	//Eliminar variables de sesión
	function destroySession(){
		Session::destroy();
		header("Location: ".URL);
	}


}

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
	
	
	
	//20170310 asunza: llama a la vista recordar contraseña
	function recordar(){
		//$data['captcha'] = $this->generaCaptcha();
		//$this->createSession($data);
		$this->view->render($this, 'recordar');
	}
	
	
	function confirmaCorreo(){
		
		$cuenta = $this->model->validaCorreo($_POST['correo']);
		echo $cuenta;
		}
	
	function generaCaptcha(){
				$vals = array(
				'word'          => 'Random word',
				'img_path'      => 'captcha/',
				'img_url'       => URL.'Index/recordar',
				'font_path'     => 'fonts/A FOR A.ttf',
				'img_width'     => '150',
				'img_height'    => 30,
				'expiration'    => 7200,
				'word_length'   => 8,
				'font_size'     => 16,
				'img_id'        => 'Imageid',
				'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
		
				// White background and border, black text and red grid
				'colors'        => array(
						'background' => array(255, 255, 255),
						'border' => array(255, 255, 255),
						'text' => array(0, 0, 0),
						'grid' => array(255, 40, 40)
				)
		);
		printf($vals);
		$cap = create_captcha(var_dump($vals));
		echo $cap['image'];
		}
	
	
	
	//Crear sesión
	function createSession($data){
		Session::setValue('captcha',$data->rand);
		/*foreach ($data as $key => $value) {
			Session::setValue($key, $value);
		}*/
	}
	
	
	/*function create_captcha($data = '', $img_path = '', $img_url = '', $font_path = '')
	{
		$defaults = array('word' => '', 'img_path' => '', 'img_url' => '', 'img_width' => '150', 'img_height' => '30', 'font_path' => '', 'expiration' => 7200);

		foreach ($defaults as $key => $val)
		{
			if ( ! is_array($data))
			{
				if ( ! isset($$key) OR $$key == '')
				{
					$$key = $val;
				}
			}
			else
			{
				$$key = ( ! isset($data[$key])) ? $val : $data[$key];
			}
		}

		if ($img_path == '' OR $img_url == '')
		{
			return FALSE;
		}

		if ( ! @is_dir($img_path))
		{
			return FALSE;
		}

		if ( ! is_writable($img_path))
		{
			return FALSE;
		}

		if ( ! extension_loaded('gd'))
		{
			return FALSE;
		}

		// -----------------------------------
		// Remove old images
		// -----------------------------------

		list($usec, $sec) = explode(" ", microtime());
		$now = ((float)$usec + (float)$sec);

		$current_dir = @opendir($img_path);

		while ($filename = @readdir($current_dir))
		{
			if ($filename != "." and $filename != ".." and $filename != "index.html")
			{
				$name = str_replace(".jpg", "", $filename);

				if (($name + $expiration) < $now)
				{
					@unlink($img_path.$filename);
				}
			}
		}

		@closedir($current_dir);

		// -----------------------------------
		// Do we have a "word" yet?
		// -----------------------------------

	   if ($word == '')
	   {
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			$str = '';
			for ($i = 0; $i < 8; $i++)
			{
				$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
			}

			$word = $str;
	   }

		// -----------------------------------
		// Determine angle and position
		// -----------------------------------

		$length	= strlen($word);
		$angle	= ($length >= 6) ? rand(-($length-6), ($length-6)) : 0;
		$x_axis	= rand(6, (360/$length)-16);
		$y_axis = ($angle >= 0 ) ? rand($img_height, $img_width) : rand(6, $img_height);

		// -----------------------------------
		// Create image
		// -----------------------------------

		// PHP.net recommends imagecreatetruecolor(), but it isn't always available
		if (function_exists('imagecreatetruecolor'))
		{
			$im = imagecreatetruecolor($img_width, $img_height);
		}
		else
		{
			$im = imagecreate($img_width, $img_height);
		}

		// -----------------------------------
		//  Assign colors
		// -----------------------------------

		$bg_color		= imagecolorallocate ($im, 255, 255, 255);
		$border_color	= imagecolorallocate ($im, 153, 102, 102);
		$text_color		= imagecolorallocate ($im, 204, 153, 153);
		$grid_color		= imagecolorallocate($im, 255, 182, 182);
		$shadow_color	= imagecolorallocate($im, 255, 240, 240);

		// -----------------------------------
		//  Create the rectangle
		// -----------------------------------

		ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg_color);

		// -----------------------------------
		//  Create the spiral pattern
		// -----------------------------------

		$theta		= 1;
		$thetac		= 7;
		$radius		= 16;
		$circles	= 20;
		$points		= 32;

		for ($i = 0; $i < ($circles * $points) - 1; $i++)
		{
			$theta = $theta + $thetac;
			$rad = $radius * ($i / $points );
			$x = ($rad * cos($theta)) + $x_axis;
			$y = ($rad * sin($theta)) + $y_axis;
			$theta = $theta + $thetac;
			$rad1 = $radius * (($i + 1) / $points);
			$x1 = ($rad1 * cos($theta)) + $x_axis;
			$y1 = ($rad1 * sin($theta )) + $y_axis;
			imageline($im, $x, $y, $x1, $y1, $grid_color);
			$theta = $theta - $thetac;
		}

		// -----------------------------------
		//  Write the text
		// -----------------------------------

		$use_font = ($font_path != '' AND file_exists($font_path) AND function_exists('imagettftext')) ? TRUE : FALSE;

		if ($use_font == FALSE)
		{
			$font_size = 5;
			$x = rand(0, $img_width/($length/3));
			$y = 0;
		}
		else
		{
			$font_size	= 16;
			$x = rand(0, $img_width/($length/1.5));
			$y = $font_size+2;
		}


		for ($i = 0; $i < strlen($word); $i++)
		{
			if ($use_font == FALSE)
			{
				$y = rand(0 , $img_height/2);
				imagestring($im, $font_size, $x, $y, substr($word, $i, 1), $text_color);
				$x += ($font_size*2);
			}
			else
			{
				$y = rand($img_height/2, $img_height-3);
				imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_path, substr($word, $i, 1));
				$x += $font_size;
			}
		}


		// -----------------------------------
		//  Create the border
		// -----------------------------------

		imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $border_color);

		// -----------------------------------
		//  Generate the image
		// -----------------------------------

		$img_name = $now.'.jpg';

		ImageJPEG($im, $img_path.$img_name);

		$img = "<img src=\"$img_url$img_name\" width=\"$img_width\" height=\"$img_height\" style=\"border:0;\" alt=\" \" />";

		ImageDestroy($im);

		return array('word' => $word, 'time' => $now, 'image' => $img);
	}*/
	
	
	////20170310 asunza
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

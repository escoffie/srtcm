<?php

class User extends Controller {

	function __construct(){
		parent::__construct();
	}

	/**
	* Inicia sesión.
	* @param Int $usar_url (default 0)
	* @return Mixed
	*/
	public function signIn($usar_url=0){

		//Datos de registro de la tabla usuarios_us
		if( !empty($_POST['usuario']) and !empty($_POST['pw_pro']) )
		{

			//Prepara los datos que se enviarán al modelo (acción signUp)
			$data['usuario'] = $_POST['usuario']; 
			$data['pw_pro'] = md5($_POST['pw_pro']);

			//No es return, sino echo, porque se pasará el valor por AJAX
			//Imprime 1 o {error} según le diga el método insert() en el modelo
			$response = $this->model->signIn($data);
			//echo "<pre>"; print_r($response); print_r($data);
			
			if(isset($response['vacio'])){
				header('Location: '.URL);	
			}

			if(($response['codigo_pro']==$data['usuario']) or ($response['email_pro']==$data['usuario'])){
				
				$this->createSession($response);

				if($usar_url==1) {
					//Cuando se accede por URL
					if(!empty($_POST['r'])) $r=ltrim($_POST['r'],'/'); else $r='Dashboard';
					$h = '';
					if($_SESSION['usuario']['direccion_pro']=='' or $_SESSION['usuario']['telefono_pro']=='' or $_SESSION['usuario']['email_pro']==''){
						$h='#perfil_btn';
					}
					header('Location: '.URL.$r.$h);
					//echo "Hola, gracias por acceder a $r ;)";
				} else {
					//Cuando se accede por Javascript (se traduce como true)
					echo 1;
				}
				
			} else {
				//print_r($response);
				echo "¡Ups! Usuario o contraseña incorrectos. Intenta de nuevo";
			}

		} else {
			echo "¡Ups! Parece que dejaste algún campo vacío. Intenta de nuevo";
		}

	}


	//Crear sesión
	function createSession($data){
		Session::setValue('usuario',$data);
		/*foreach ($data as $key => $value) {
			Session::setValue($key, $value);
		}*/
	}

	//Eliminar variables de sesión
	function destroySession(){
		Session::destroy();
		header("Location: ".URL);
	}


}

<?php
/*Todos los demás controladores heredarán de esta clase*/

class Controller {

	function __construct(){
		Session::init();
		$this->view = new View();
		$this->loadModel();
	}

	function loadModel(){
		//Por ejemplo, models/User_model.php
		$model = get_class($this).'_model';
		$rutaModelo = 'models/'.$model.'.php';

		if(file_exists($rutaModelo)){
			require_once $rutaModelo;
			$this->model = new $model();
		}
	}

}
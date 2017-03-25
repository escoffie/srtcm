<?php

class View {

	function render($controller, $view, $data=array("Puesto"=>"Compadrito","Cosa"=>"Esa")){
		extract($data);
		//Para obtener el nombre de la clase y luego cargarla
		$controller = get_class($controller);
		//Ejemplo: views/Usuario/index.php
		require_once './views/'.$controller.'/'.$view.'.php';
	}

}
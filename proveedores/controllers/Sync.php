<?php

class Sync extends Controller {

	function __construct(){
		parent::__construct();
	} 
	/* Termina function __construct() */

	function index(){

		if( Session::exists() ) {
			if(!isset($data)){
				$data = array('submenu'=>'Sync_submenu');	
			}
			$this->view->render($this, 'index', $data);
		} else {
			header("Location: ".URL);
		}

	} 
	/* Termina function index() */
	
	function upload($respuesta=true){
		$volver =  "<p><a href=\"".URL."Sync\">Volver</a></p>";
		if(isset($_FILES['csv']) and $_FILES['csv']['tmp_name']!=""){
			$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
			if(in_array($_FILES['csv']['type'],$mimes)){
				$dir = './csv/';
				$csv = $dir . $_FILES['csv']['name'];
				if(move_uploaded_file($_FILES['csv']['tmp_name'], $csv)){
					$respuesta = $this->model->loadData($csv);
					if($respuesta==1){
						//header("Location: ".URL."Sync");
						echo "<p>El archivo $csv se importó exitosamente</p>$volver";
					} else {
						echo "<p>Respuesta no es 1</p><pre>";
						print_r($respuesta);
					}
				} else {
					die("Hubo un problema al subir el archivo.$volver");	
				}
			} else {
				die("Archivos de tipo ".$_FILES['file']['type']." no son admitidos. $volver");	
			}
		} else {
			die("No se subió ningún archivo. $volver");	
		}
	}
	/* Termina function upload()*/
	
	function bulkCSV($proceso='listar'){
		
		$dir = './csv/';
		$arraydearchivos = array_diff(scandir($dir), array('..','.'));
		
		if($proceso=='listar'){
			var_dump($arraydearchivos);
		} 
		if($proceso=='importar'){
			$cuantos = sizeof($arraydearchivos);
			if($cuantos>0){
				sleep(5);
				$error = array();
				$exito = array();
				foreach($arraydearchivos as $archivo){
					$respuesta = $this->model->loadData($dir.$archivo);
					if($respuesta!=1) $error[] = "ERROR: ". $respuesta;
					else $exito[] = "<p>$archivo se importó con éxito</p>";
				}
				if(sizeof($error)>0){
					print_r($error);
				} else {
					print_r($exito);
				}
				echo "<p><a href=\"".URL."Sync\">Volver</a></p>";
			}
		}
	}
	/* Termina bulkCSV() */
	
	function listarArchivosLocales($proceso='listar'){
		
		$dir = './csv/';
		$arraydearchivos = array_diff(scandir($dir), array('..','.'));
		
		if($proceso=='listar'){
			//var_dump($arraydearchivos);
		} 
		$resultado = array(
			'archivos' => $arraydearchivos,
			'submenu' => 'Sync_submenu'
			);
		
		$this->view->render($this, 'listarArchivos', $resultado);
	}
	/* Termina listarArchivosLocales */
	
	function listarArchivosRemotos(){
		$parametros = array(
			'ftp_server'=>'148.223.222.228',
			'ftp_user_name'=>'comaweb',
			'ftp_user_pass'=>'Envios98',
			);	
		$resultado = array(
			'archivos' => $this->model->ftpListFiles($parametros),
			'submenu' => 'Sync_submenu'
			);
		
		$this->view->render($this, 'listarArchivosRemotos', $resultado);
	}
	
	function getFileByName($file){
		$parametros = array(
			'ftp_server'=>'148.223.222.228',
			'ftp_user_name'=>'comaweb',
			'ftp_user_pass'=>'Envios98',
			'ftp_filename'=>$file,
			);	
		$respuesta = $this->model->ftpGetFileByName($parametros);
		
		echo $respuesta;
	}
	
	function cronSync(){
		$parametros = array(
			'ftp_server'=>'148.223.222.228',
			'ftp_user_name'=>'comaweb',
			'ftp_user_pass'=>'Envios98',
			);	
		$respuesta = $this->model->ftpGetLastFile($parametros);
		
		echo "<pre>";
		print_r($respuesta);
	}
	
} 
/* Termina class Sync */
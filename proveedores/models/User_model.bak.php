<?php
include_once './models/PipedriveAPI_model.php'; 

class User_model extends PipedriveAPI_model {
	
	function __construct(){
		parent::__construct();
	}

	/**
	* signUp
	* Guarda un nuevo usuario no activado, con los datos mínimos
	* @param Array $data
	* @return Array
	*/
	function signUp($data){
		//Array keys: organizacion_us, nombre_us, correo_us, telefono_us, contrasena_us
		return $this->db->insert('usuarios_us', $data); //array
	}

	/**
	* signIn
	* Firma acceso usando correo y contraseña
	* @param Array $data correo_us, contrasena_us
	* @return Array
	*/
	function signIn($data){
		$recordset = $this->db->select('id_us, organizacion_us, nombre_us, correo_us, telefono_us, direccion_us, foto_us, estado_us','usuarios_us', 'correo_us="'.$data['correo_us'].'" AND contrasena_us="'.$data['contrasena_us'].'" AND estado_us>0');			
		return $recordset;
	}
	
	/**
	* signInOnUpdate
	* Firma acceso usando sólo correo y secreto, cuando se actualiza
	* @param Array $data correo_us, contrasena_us
	* @return Mixed (Array/bool false)
	*/
	/*function signInOnUpdate($data, $secreto=false){
		if($secreto==true){
			return $this->db->select('id_us, organizacion_us, nombre_us, telefono_us, correo_us, direccion_us, foto_us','usuarios_us', "id_us='$data'");			
		} else {
			return false;
		}
	}*/

	function selectUser($id_us, $md5=false){
		if(!$md5) {
			$recordset = $this->db->select('id_us, organizacion_us, nombre_us, correo_us, telefono_us, direccion_us, foto_us, estado_us', 'usuarios_us', 'id_us='.$id_us);
		} else {
			$recordset = $this->db->select('id_us, organizacion_us, nombre_us, correo_us, telefono_us, direccion_us, foto_us, estado_us', 'usuarios_us', "MD5(id_us)='".$id_us."'");
		}
		return $recordset;
	}

	function selectUserByEmail($correo_us, $md5=true){
		if(!$md5) {
			$recordset = $this->db->select('id_us, organizacion_us, nombre_us, correo_us, telefono_us, direccion_us, foto_us, estado_us', 'usuarios_us', "correo_us='$correo_us'");
		} else {
			$recordset = $this->db->select('id_us, organizacion_us, nombre_us, correo_us, telefono_us, direccion_us, foto_us, estado_us', 'usuarios_us', "MD5(correo_us)='".$correo_us."'");
		}
		return $recordset;
	}

	function updateUser($data, $id_us, $md5=false){
		if(!$md5){
			$resultado = $this->db->update('usuarios_us', $data, "id_us=".$id_us);
		} else {
			$resultado = $this->db->update('usuarios_us', $data, "MD5(id_us)='".$id_us."'");
		}
		return $resultado; //booleano
	}

	/*function createFirstProject($data){
		return $this->db->insert('proyectos_pr', $data);
	}

	function createProject($data){
		return $this->db->insert('proyectos_pr', $data);
	}

	function selectProjectsById_us($id_us){
		$resultado = $this->db->select('*', 'proyectos_pr', 'id_us='.$id_us);
		return $resultado;
	}*/

	function truncate(){
		$this->db->query('TRUNCATE TABLE usuarios_us');
		$this->db->query('TRUNCATE TABLE proyectos_pr');
		$this->db->query('TRUNCATE TABLE pipedrive_pd');
		$this->db->query('TRUNCATE TABLE mensajes_msg;');
		return "Vacío";
	}

}
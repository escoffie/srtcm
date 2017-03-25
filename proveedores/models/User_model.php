<?php

class User_model extends Model {
	
	function __construct(){
		parent::__construct();
	}

	/**
	* signIn
	* Firma acceso usando correo y contraseña
	* @param Array $data correo_us, contrasena_us
	* @return Array
	*/
	function signIn($data){
		
		$campo='-1';
		if (!filter_var($data['usuario'], FILTER_VALIDATE_EMAIL) === false) {
			$campo = 'email_pro';
		} else if (is_numeric($data['usuario'])) {
			$campo = 'codigo_pro';
		} else {
			$recordset[]=false;	
		}
		
		$consulta = "SELECT 
			id_rol, 
			codigo_pro, 
			rfc_pro, 
			razonsocial_pro, 
			direccion_pro,
			telefono_pro,
			email_pro,
			avatar_pro
		FROM proveedores_pro
		WHERE $campo='$data[usuario]' AND pw_pro='$data[pw_pro]' AND estatus_pro>0";
		$resultado = $this->db->query($consulta);
		if($resultado->num_rows>0){
			$recordset=$resultado->fetch_assoc();
			$recordset['id_niv']=1;
			$recordset['secreto']=md5(time());
			if($recordset['codigo_pro']>0){
				$q = "INSERT INTO log (codigo_pro, sid, timestamp, tipo, accion) VALUES ($recordset[codigo_pro], '$recordset[secreto]', '".date('Y-m-d H:i:s')."', 'Inicio', 'Log in')";
				$r = $this->db->query($q);
			}
		} else {
			$recordset['vacio'] = true;
		}
		if(DEBUG==true){
			$recordset['data']=$data;
			$recordset['consulta']=$consulta;
		}
		return $recordset;
	}
	

}
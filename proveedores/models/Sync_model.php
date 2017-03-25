<?php

class Sync_model extends Model {

	/*
	* Función constructora
	*/
	function __construct(){
		parent::__construct();
	}
	/* Termina __construct() */
	
	function loadData($archivo){
		# Referencia: http://stackoverflow.com/questions/15271202/mysql-load-data-infile-with-on-duplicate-key-update
		
		ob_start();
		header( 'Content-type: text/html; charset=utf-8' );
		
		$continuar=true;
		
		$mensaje = "<h1>$archivo</h1>";
		
		# Verificar si ya fue importado antes contra la tabla __origen_importado_imp
		$q_existe = "SELECT * FROM __origen_importado_imp WHERE archivo_imp='$archivo'";
		$r_existe = $this->db->query($q_existe);
		if($r_existe->num_rows > 0){
			$mensaje .= "<p style=\"color:orange\">El archivo <strong>$archivo</strong> ya fue importado con anterioridad</p><pre>";
			$r = $r_existe->fetch_assoc();
			foreach($r as $k => $v){
				$mensaje.="<p>$k = $v</p>";	
			}
			$continuar=false;
			//print_r($r);
			$mensaje .="</pre><p>Proceso terminado</p>";
		}
		
		
		if($continuar==true){
			
			$mensaje .= "<p>Antes de importar, se verifica la integridad del archivo. Se buscan 26 columnas por cada fila.</p>";
			$mensaje .= "<p>Ruta del archivo: $archivo</p>";
			
			if(($gestor = fopen($archivo, "r")) !== false) {
			
				$mensaje .= "<p style=\"color:blue;\">Archivo abierto. Continúa la ejecución</p>";
				
				# 0. Preparando el archivo para verificar si tiene los 26 campos que debe tener antes de hacer el load data
				$debenser = 26; //número de columnas
				$errordecolumnas=true; //se asume que el dato es incorrecto para comenzar
				$numerodefila=1;
				
				while($line = fgetcsv($gestor,0,"|")){
					
					$numcols = count($line);
					
					if($numcols == $debenser or $numcols==1){
						$errordecolumnas=false;
					} else {
						$errordecolumnas=true;
						break;
					}
					
					$numerodefila++;
						
				}
				
				if($errordecolumnas==true){
					$mensajedeerror="<h2 style=\"color:red;\">Error en la línea $numerodefila del archivo $archivo. Número de columnas no corresponde ($numcols en vez de $debenser columnas)</h2>";
					$mensaje .=$mensajedeerror;	
					$qerror = "INSERT INTO __origen_importado_imp (fecha_imp, archivo_imp, affected_rows_imp, error_imp) VALUES ('".date('Y-m-d H:i:s')."', '$archivo', 0, '$mensajedeerror');";
					$rerror = $this->db->query($qerror);
				} else {
					$mensaje .= "<p style=\"color:green;\">Archivo válido. Continúa la ejecución</p>";
				}
			
			} else {
				$mensajedeerror = "<p style=\"color:red;\">No se pudo abrir el archivo $archivo</p>";
				$mensaje .= $mensajedeerror;
				$qerror = "INSERT INTO __origen_importado_imp (fecha_imp, archivo_imp, affected_rows_imp, error_imp) VALUES ('".date('Y-m-d H:i:s')."', '$archivo', 0, '$mensajedeerror');";
				$rerror = $this->db->query($qerror);
			}
			
			if($errordecolumnas==false){
				
				$mensaje .= "<p>Paso 1. Crea tabla temporal</p>";
				
				# 1. Crear tabla temporal con la misma estructura que la original
				$q1 = "CREATE TEMPORARY TABLE __temp LIKE __origen;";
				$r1 = $this->db->query($q1);
				
				
				$mensaje .= "<p>Paso 2. Carga CSV</p>";
				
				# 2. Carga el CSV en la tabla temporal
				if($r1){
					$q2 = "LOAD DATA LOCAL INFILE '$archivo'
					IGNORE INTO TABLE `__temp`
					CHARACTER SET utf8
					FIELDS TERMINATED BY '|'
					LINES TERMINATED BY '\\n'
					(`TRANSACTION_ID`, `empresa`, `nombreempresa`, `sucursal`, `nomsucursal`, @d, `FOLIO_FACTURA`, `articulo`, `descripcion`, `capcidad`, `empaque`, `medida`, `codbarrapieza`, `codbarracaja`, `sku`, `codprovedor`, `nombreprov`, `rfcprov`, `fac_monto`, `fac_cantdidad`, `facunidadmedida`, `codfamilia`, `descfamilia`, `codsubfami`, `descsubfamilia`, `TRANSACTION_SOURCE_NAME`)
					SET `fec_factura` = STR_TO_DATE(@d, '%d-%b-%Y');";
					
					$r2 = $this->db->query($q2);
					
					$r2_cleanup = $this->db->query('DELETE FROM __temp WHERE TRANSACTION_ID=0');
				}
				
				
				$mensaje .= "<p>Paso 3. Copiar la data usando ON DUPLICATE KEY UPDATE</p>";
				
				# 3. Copiar la data usando ON DUPLICATE KEY UPDATE
				if($r2){
					$q3 = "INSERT INTO __origen 
					SELECT * FROM __temp AS t
					ON DUPLICATE KEY UPDATE
					empresa=t.empresa, nombreempresa=t.nombreempresa, sucursal=t.sucursal, nomsucursal=t.nomsucursal, fec_factura=t.fec_factura, FOLIO_FACTURA=t.FOLIO_FACTURA, articulo=t.articulo, descripcion=t.descripcion, capcidad=t.capcidad, empaque=t.empaque, medida=t.medida, codbarrapieza=t.codbarrapieza, codbarracaja=t.codbarracaja, sku=t.sku, codprovedor=t.codprovedor, nombreprov=t.nombreprov, rfcprov=t.rfcprov, fac_monto=t.fac_monto, fac_cantdidad=t.fac_cantdidad, facunidadmedida=t.facunidadmedida, codfamilia=t.codfamilia, descfamilia=t.descfamilia, codsubfami=t.codsubfami, descsubfamilia=t.descsubfamilia, TRANSACTION_SOURCE_NAME=t.TRANSACTION_SOURCE_NAME";
					$r3 = $this->db->query($q3);
					$afectadas = $this->db->affectedRows();
				}
				
				
				$mensaje .= "<p>Paso 4. Eliminar tabla temporal</p>";
				
				# 4. Eliminar tabla temporal
				if($r3){
					$q4 = "DROP TEMPORARY TABLE IF EXISTS __temp;";
					$r4 = $this->db->query($q4);
				}
				
				
				$mensaje .= "<p>Paso 5. Inserta en la tabla __origen_importado_imp el nombre del archivo CSV ejecutado para que no lo vuelva a llamar</p>";
				
				# 5. Inserta en la tabla __origen_importado_imp el nombre del archivo CSV ejecutado para que no lo vuelva a llamar
				if($r4){
					$q5 = "INSERT INTO __origen_importado_imp (fecha_imp, archivo_imp, affected_rows_imp) VALUES ('".date('Y-m-d H:i:s')."', '$archivo', $afectadas);";
					$r5 = $this->db->query($q5);
				}
				
				
				$mensaje .= "<p>Paso 6. Rutinas de sincronización</p>";
				
				# 6. Rutinas de sincronización
				if($r4 and $r5){
					$q6 = "INSERT IGNORE INTO surticom_crm.transacciones_tra (
			transaccion_tra,
			transactionsourcename_tra,
			codigo_fam,
			codigo_sub,
			codigo_sucursal_suc,
			codigo_pro,
			codigo_art,
			unidadmedida_tra,
			cantidad_tra,
			monto_tra,
			factura_folio_tra,
			factura_fecha_tra
		 ) SELECT 
			a.TRANSACTION_ID,
			a.TRANSACTION_SOURCE_NAME,
			a.codfamilia,
			a.codsubfami,
			a.sucursal,
			a.codprovedor,
			a.articulo,
			a.facunidadmedida,
			a.fac_cantdidad,
			a.fac_monto,
			a.FOLIO_FACTURA,
			a.fec_factura
		FROM __origen AS a
		ORDER BY a.FOLIO_FACTURA ASC;
		
		INSERT IGNORE INTO surticom_crm.familias_fam (
			codigo_fam, 
			nombre_fam
		) SELECT codfamilia, descfamilia
		FROM  __origen
		WHERE 1 
		GROUP BY codfamilia
		ORDER BY descfamilia;
		
		INSERT IGNORE INTO surticom_crm.subfamilias_sub(
			codigo_fam,
			codigo_sub,
			nombre_sub
		)
		SELECT codfamilia, codsubfami, descsubfamilia
		FROM  __origen
		WHERE 1 
		group by codsubfami
		ORDER BY codfamilia, descsubfamilia;
		
		INSERT IGNORE INTO surticom_crm.articulos_art
		(
			codigo_fam,
			codigo_sub,
			codigo_art,
			nombre_art,
			capacidad_art,
			empaque_art,
			unidadmedida_art,
			codigodebarrapieza_art,
			codigodebarracaja_art,
			sku_art
			)
		SELECT 
		codfamilia AS codigo_fam, 
		codsubfami AS codigo_sub, 
		articulo AS codigo_art,
		descripcion AS nombre_art,
		capcidad AS capacidad_art,
		empaque AS empaque_art,
		medida AS unidadmedida_art,
		codbarrapieza AS codigodebarrapieza_art,
		codbarracaja AS codigodebarracaja_art,
		sku AS sku_art
		FROM __origen
		GROUP BY codigo_art
		ORDER BY codigo_fam, codigo_sub, nombre_art, unidadmedida_art, capacidad_art;
		
		
		INSERT INTO surticom_crm.proveedores_pro (
			id_rol,
			estatus_pro,
			fecha_pro,
			codigo_pro,
			rfc_pro,
			razonsocial_pro,
			direccion_pro,
			telefono_pro,
			email_pro,
			pw_pro,
			avatar_pro
			)
		
		SELECT
		2,
		1,
		'2016-06-06 17:30:00',
		codprovedor AS codigo_pro,
		rfcprov AS rfc_pro,
		nombreprov AS razonsocial_pro,
		null,
		null,
		null,
		md5('12'),
		null
		FROM __origen
		WHERE codprovedor NOT IN (SELECT codigo_pro FROM proveedores_pro)
		GROUP BY (codprovedor)
		ORDER BY nombreprov;
		
		
		INSERT IGNORE INTO
		surticom_crm.pro_fam_sub_pfs 
		(
			codigo_pro,
			codigo_fam,
			codigo_sub
			)
		SELECT 
		codprovedor AS codigo_pro,
		codfamilia AS codigo_fam,
		codsubfami AS codigo_sub
		FROM __origen
		GROUP BY CONCAT(codfamilia, codsubfami,codprovedor)
		ORDER BY codprovedor,codfamilia,codsubfami;
		
		
		INSERT IGNORE INTO surticom_crm.empresa_sucursal_proveedor_esp
		(codigo_sucursal_suc,
		 codigo_pro)
		SELECT sucursal, codprovedor
		FROM  __origen 
		GROUP BY CONCAT( sucursal, codprovedor ) 
		ORDER BY  __origen.codprovedor DESC;
		";
					$r6 = $this->db->multi_query($q6);
		
				}
				
				$this->db->next_result();
				
				
				$mensaje .= "<p>Paso 7. Regresa el resultado booleano</p>";
				$mensaje .= "<p>Filas afectadas: $afectadas</p>";
				$mensaje .= "<hr>";
				
				# 7. Regresa el resultado booleano
				if(!$r6){
					$mensaje .= "Error en el paso 7. Línea ".__LINE__." del archivo ".__FILE__;
				}
				
				$segundos = 3;
				
			}
		}

		//PHPMailer
		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';
		
		//$mail->SMTPDebug = 3;
		$mail->isSMTP();
		$mail->Host = SERVIDORSALIDA;
		$mail->SMTPAuth = true;
		$mail->Username = USUARIO;
		$mail->Password = PASSWORD;
		$mail->SMTPSecure = "";
		if(PUERTOSALIDA==587){
			//$mail->SMTPSecure = "tls";	
		}
		$mail->Port = PUERTOSALIDA;
		
		$mail->From = REMITENTE;
		$mail->FromName = REMITENTENOMBRE;
		$mail->addAddress("bernardoescoffie@gmail.com");
		$mail->addAddress("reporte_crmproveedores@coma.com.mx");
		$mail->isHTML(true);
		$mail->Subject = "CRM Surticoma - Sincronización " . date('Y-m-d H:i:S');
		$mail->Body = "$mensaje";
		
		$mail->send();
	
		return $mensaje;
	
	}
	/* Termina loadData() */
	
	function ftpListFiles($parametros){
		extract($parametros);
		// establecer una conexión básica
		$conn_id = ftp_connect($ftp_server);
		
		// iniciar sesión con nombre de usuario y contraseña
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		
		// Obtener los archivos contenidos en el directorio actual
		$archivos = ftp_rawlist($conn_id, "-t .");
		
		$results = array();
		foreach ($archivos as $line) {
			list($perms, $links, $user, $group, $size, $d1, $d2, $d3, $name) =
				preg_split('/\s+/', $line, 9);
			$stamp = strtotime(implode(' ', array($d1, $d2, $d3)));
			$results[] = array('name' => $name, 'timestamp' => $stamp, 'size' => $size, 'raw' => $line);
		}
		
		usort($results, function($a, $b) { return $b['timestamp'] - $a['timestamp']; });		
			
		return $results;
		
		ftp_close($conn_id);
	}
	/* Termina ftpListFiles() */
		
	function ftpGetFileByName($parametros){
		
		$respuesta = "Aquí no ha pasado nada";
		
		extract($parametros);
		// establecer una conexión básica
		$conn_id = ftp_connect($ftp_server);
		
		// iniciar sesión con nombre de usuario y contraseña
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		
		// Obtener el archivo pasado como parámetro
		$archivo = $ftp_filename;
		
		$respuesta = $archivo;
		
		// Si el archivo existe
			
		$local_file = './csv/'.str_replace('./','',$archivo);
		
		if (ftp_get($conn_id, $local_file, $archivo, FTP_BINARY)) {
			$respuesta = "Se ha guardado satisfactoriamente en $local_file\n";
			$transferencia=true;
		} else {
			$respuesta = "Ha habido un problema al transferir el archivo $server_file\n";
		}	
		
		if($transferencia){
			$respuesta=$this->loadData($local_file);
		}
		ftp_close($conn_id);
		return $respuesta;
	}
	/* Termina ftpGetFileByName() */
		
	function ftpGetLastFile($parametros){
		extract($parametros);
		// establecer una conexión básica
		$conn_id = ftp_connect($ftp_server);
		
		// iniciar sesión con nombre de usuario y contraseña
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		
		// Obtener los archivos contenidos en el directorio actual
		$archivos = ftp_nlist($conn_id, ".");
		
		if(!count($archivos)){
			die("Directorio vacío");	
		} else {
			$archivosCSV = array();
			foreach($archivos as $archivo){
				if( !preg_match('~\w+.csv$~ism', $archivo)) continue;	
				$archivosCSV[] = $archivo;
			}
			$masReciente = array(
				'tiempo'=>0,
				'archivo'=>null
			);
			foreach($archivosCSV as $archivoCSV){
				$tiempo = ftp_mdtm($conn_id, $archivoCSV);
				if($tiempo > $masReciente['tiempo']){
					$masReciente['tiempo'] = $tiempo;
					$masReciente['archivo'] = $archivoCSV;
				}
			}
		}
		
		$server_file = $masReciente['archivo'];
		$local_file = './csv/'.str_replace('./','',$server_file);
		
		$respuesta = array();
		$transferencia = false;
		if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
			$respuesta[] = "Se ha guardado satisfactoriamente en $local_file\n";
			$transferencia=true;
		} else {
			$respuesta[] = "Ha habido un problema al transferir el archivo $server_file\n";
		}	
		if($transferencia){
			$respuesta[]=$this->loadData($local_file);
		}
		
		$respuesta = implode("\n", $respuesta);
		
		return $respuesta;
		
		ftp_close($conn_id);
	}
	/* Termina ftpGetLastFile() */
		
}
<?php require ('./public/php/Dashboard_header.php'); ?>
<h1>Listado de archivos en servidor remoto.</h1>
<table class="table display" id="listarArchivosRemotos">
	<thead>
        <tr>
            <th>Importar</th>
            <th nowrap>Archivo</th>
            <th nowrap>Fecha</th>
            <th nowrap>Peso</th>
            <th nowrap>Importado</th>
            <th nowrap>Registros</th>
            <th>Mensaje</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Importar</th>
            <th>Archivo</th>
            <th>Fecha</th>
            <th>Peso</th>
            <th>Importado</th>
            <th>Registros</th>
            <th>Mensaje</th>
        </tr>
    </tfoot>
    <tbody>
		<?php foreach($archivos as $archivo){
			$comparaConImportados = comparaConImportados($archivo['name']);
			if($comparaConImportados['fecha_imp']!='') $disabled="disabled"; else $disabled="";
        ?>
        <tr>
            <td><a class="btn btn-warning importar-datos <?php echo $disabled; ?>" href="<?php echo URL.'Sync/getFileByName/'.$archivo['name']; ?>"><i class="fa fa-cloud-download" aria-hidden="true"></i> Importar</a></td>
            <td> <?php echo $archivo['name']; ?></td>
            <td><?php echo date('Y-m-d H:i:s', $archivo['timestamp']); ?></td>
            <td><?php echo number_format($archivo['size']/1024,1); ?> kb</td>
            <td><?php echo $comparaConImportados['fecha_imp']; ?></td>
            <td><?php echo $comparaConImportados['affected_rows_imp']; ?></td>
            <td><?php echo utf8_encode(strip_tags($comparaConImportados['error_imp'])); ?></td>
        </tr>
        <?php	
        }
        ?>
    </tbody>
</table>
<?php require ('./public/php/Dashboard_footer.php'); ?>
<?php
function comparaConImportados($archivo_imp){
	$q = "SELECT fecha_imp, affected_rows_imp, error_imp FROM __origen_importado_imp WHERE archivo_imp='./csv/$archivo_imp'";
	$my = new MySQLi(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$rs = $my->query($q) or die("Error en $q");
	if($rs->num_rows>0){
		$respuesta = $rs->fetch_assoc();
	} else {
		$respuesta['fecha_imp'] = '';
		$respuesta['affected_rows_imp'] = '';
		$respuesta['error_imp'] = '';
	}
	$respuesta['q']=$q;
	return $respuesta;	
}
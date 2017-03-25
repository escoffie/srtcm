<?php require ('./public/php/Dashboard_header.php'); ?>
<table class="table">
	<tr>
		<th>Archivo</th>
	</tr>
    <?php foreach($archivos as $archivo){
	?>
    <tr>
    	<td><a href="<?php echo URL.'csv/'.$archivo; ?>" class="btn btn-success" target="_blank">Ver</a> <?php echo $archivo; ?></td>
    </tr>
    <?php	
	}
	?>
</table>
<?php require ('./public/php/Dashboard_footer.php'); ?>

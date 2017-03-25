<?php require ('./public/php/Dashboard_header.php'); ?>

<div class="container">

	<div class="row">
		<div class="text-center col-md-6 col-md-offset-3">
        	<h1>Importar CSV</h1>
            <form action="<?php echo URL; ?>Sync/upload" method="post" enctype="multipart/form-data">
            	<div class="form-group">
                	<input class="form-control" type="file" name="csv" id="csv">
                </div>
                <button class="btn btn-success">Enviar</button>
            </form>
        </div>
	</div>

</div>


<?php require './public/php/Dashboard_footer.php'; ?>


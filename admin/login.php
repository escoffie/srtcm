<?php 
error_reporting(E_ERROR);

require_once("../Connections/cnx.php");

session_start();
 
if (isset($_GET['login'])) {
     // Only load the code below if the GET
     // variable 'login' is set. You will
     // set this when you submit the form
 
     mysql_select_db($database_cnx);
	 $q = "SELECT * FROM usuarios_us WHERE user = '$_POST[username]' AND password='".md5($_POST['password'])."'";
	 $m = mysql_query($q);
	 $r = mysql_fetch_assoc($m);
	 $n = mysql_num_rows($m);
	 
	 
	 if ($n>0) {
         // Load code below if both username
         // and password submitted are correct
 
		$_SESSION['loggedin'] = 1;
		$_SESSION['KCFINDER'] = array(
			'disabled' => false
		);
		foreach($r as $k => $v){
			$_SESSION[$k]=$v; 
		}
		// Set session variable
		
		mysql_free_result($m);
		
		header("Location: index.php");
		exit;
		// Redirect to a protected page
 
     } //else echo "$q";
     // Otherwise, echo the error message
 
}
 
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<title>Surticoma: Acceso restringido</title>
</head>

<body>
<div class="container">
	<div class="row">
    	<div class="col-xs-12 col-sm-6 col-md-4 col-xs-offset-0 col-sm-offset-3 col-md-offset-4">
        	<p class="text-center"><img src="../img_constantes/logo_paginmterior.png" class="img-responsive img-center"></p>
            <div class="panel panel-default">
            	<div class="panel-heading">Acceder</div>
                <div class="panel-body">
                    <form action="?login=1" method="post">
                        <div class="form-group">
                            <label for="username">Usuario</label>
                            <input class="form-control" type="text" name="username" />
                        </div>
                        <div class="form-group">
                            <label for="password">Usuario</label>
                            <input class="form-control" type="password" name="password" />
                        </div>
                        <div class="form-group">
                            <input class="form-control" type="submit" value="Entrar" />
                        </div>
                    </form>
                
                </div>
            </div>
        
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
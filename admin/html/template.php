<!DOCTYPE HTML>
<html>
    <head>
    	<meta http-equiv="content-type" content="text/html;charset=utf-8" />  
    	<title><?php echo $title_1 ?> - SurtiCOMA</title>
        <link href="assets/style.css" rel="stylesheet" type="text/css" />
        <?php echo Xcrud::load_css() ?>
        <link href="../xcrud/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    </head>
    
    <body>
        <div id="page">
            <div id="menu"><?php include(dirname(__FILE__).'/menu.php') ?></div>
            <div id="content">
                <?php if($_SESSION['level']>$level) die('Nivel de acceso insuficiente'); ?>
                <div class="clr">&nbsp;</div>
                <h1><?php echo $title_1 ?> <!--<small><?php echo $title_2 ?></small>--></h1>
                <p><?php echo $description ?></p>
                <!--<pre class="brush: php"><?php echo htmlspecialchars($code) ?></pre>-->
                <?php include($file) ?>
                <div class="clr">&nbsp;</div>
                <!--<div><pre><?php //echo print_r($_SESSION); ?></pre></div>-->

            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <?php echo Xcrud::load_js() ?>
        <script src="../xcrud/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>
<?php
$url = (isset($_GET['url'])) ? $_GET['url'] : 'Index/index';
$url = explode("/", $url);
include('./../xcrud/xcrud.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo CL_NOMBRE; ?>: Dashboard</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo BOWER; ?>bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo BOWER; ?>bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
    
    <!-- 20170314 asunza Multi-Select CSS -->
    <link href="<?php echo BOWER; ?>bower_components/multiSelect/dist/css/bootstrap-select.min.css" rel="stylesheet">


    <!-- Timeline CSS -->
    <link href="<?php echo BOWER; ?>dist/css/timeline.css" rel="stylesheet">

     <!--DataTables CSS -->
<!--    <link href="<?php echo BOWER; ?>bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.12/fc-3.2.2/sc-1.4.2/datatables.min.css"/>
    <!-- DataTables Responsive CSS -->
    <link href="<?php echo BOWER; ?>bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo BOWER; ?>dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- jQuery UI -->
    <link href="<?php echo URL; ?>../xcrud/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
    <link href="<?php echo URL; ?>../xcrud/plugins/timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet">

    <!-- Surticoma CSS -->
    <link href="<?php echo URL; ?>public/css/surticoma.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="<?php echo BOWER; ?>bower_components/morrisjs/morris.css" rel="stylesheet">
    
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    
    <!-- Bootstrap Tour -->
    <link rel="stylesheet" type="text/css" href="<?php echo URL; ?>public/js/bootstrap-tour/css/bootstrap-tour.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script src="https://use.fontawesome.com/0ca6f7d5e2.js"></script>
    
    <?php echo Xcrud::load_css() ?>

<?php require ('./public/php/zopim.php'); ?>
<?php require ('./public/php/analyticstracking.php'); ?>

</head>

<body style="padding-top:50px;">

    <div id="wrapper">

        <!-- Navigation -->
        <nav id="navbar-principal" class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">       
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Intercalar navegación</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!--<a class="navbar-brand" href="<php echo URL; ?>Dashboard"><php echo CL_NOMBRE; ?></a>-->
                <a class="navbar-brand" href="<?php echo URL; ?>Dashboard"><img id="img-logo" src="<?php echo URL; ?>public/images/logo.png" alt="Logo"></a>
            	</div>
            <!-- /.navbar-header -->
            
  <!-- Super Admins -->
  <?php if($_SESSION['usuario']['codigo_pro']==0){ ?>
  <ul class="nav navbar-nav">
    <li>
        <a href="<?php echo URL; ?>Dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
    </li>
    <li>
        <a href="<?php echo URL; ?>Dashboard/Proveedores"><i class="fa fa-user fa-fw"></i> Proveedores</a>
    </li>
    <li>
        <a href="<?php echo URL; ?>Dashboard/Sucursales"><i class="fa fa-map-marker fa-fw"></i> Sucursales</a>
    </li>
    <li>
        <a href="<?php echo URL; ?>Dashboard/Proveedores/Surticoma"><i class="fa fa-user-secret fa-fw"></i> Usuarios</a>
    </li>
    <li>
        <a href="<?php echo URL; ?>Dashboard/Configura/"><i class="glyphicon glyphicon-wrench"></i> Configuraci&oacute;n</a>
    </li>
  </ul> 
 <?php } ?>
 			
               <ul class="nav navbar-top-links navbar-right">                      
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="nombre_de_usuario">
                     <?php if($_SESSION['usuario']['codigo_pro']>0){ ?>
                 			<img src=""  id="img_prove" alt="Imagen Proveedor">
            		<?php }?>
                        <i class="fa fa-user fa-fw"></i> <?php echo $_SESSION['usuario']['razonsocial_pro']; ?> <i class="fa fa-caret-down"> </i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <!--<li><a href="#tab-e" id="perfil_btn" aria-controls="tab-e" role="tab" data-toggle="tab"><i class="fa fa-user fa-fw"></i> Perfil de usuario</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Configuración</a>
                        </li>
                        <li class="divider"></li>-->
                        <li><a id="tour-start" href="<?php echo URL; ?>Dashboard"><i class="fa fa-question-circle fa-fw"></i> ¿Cómo funciona?</a>
                        <li><a id="cerrar-sesion" href="<?php echo URL; ?>User/destroySession"><i class="fa fa-sign-out fa-fw"></i> Cerrar sesión</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
			<?php 
            $class_submenu = '';
            if(isset($submenu)){ 
                $class_submenu = 'filtro';
			?>
             
            <div class="navbar-default sidebar" role="navigation" id="panel_filtro" style="margin-top:100px !important;">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
								<?php require ('./public/php/'.$submenu.'.php'); ?>
                        </li>

                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
			<?php
            }
            ?>
        </nav>

        <div id="page-wrapper" style="padding-top:40px;" class="<?php echo $class_submenu; ?>">
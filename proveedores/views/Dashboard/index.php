<?php require ('./public/php/Dashboard_header.php'); ?>

<div>
	<div class="row">
		<div class="col-md-12" id="monitorFiltro">
        	<div class="alert alert-success">
            	Hola, <?php echo $_SESSION['usuario']['razonsocial_pro']; ?>
            </div>
        </div>
	</div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#market-share" aria-controls="market-share" role="tab" data-toggle="tab"><i class="fa fa-pie-chart"></i> Market Share</a></li>
    <li role="presentation"><a href="#venta-por-dia" aria-controls="venta-por-dia" role="tab" data-toggle="tab"><i class="fa fa-line-chart"></i> Venta por día</a></li>
    <li role="presentation"><a href="#comparativo" aria-controls="comparativo" role="tab" data-toggle="tab"><i class="fa fa-balance-scale"></i> Comparativo</a></li>
    <li role="presentation"><a href="#perfil" aria-controls="perfil" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> Perfil de usuario</a></li>
    <?php if(DEBUG==true){ ?>
    <li role="presentation"><a href="#dev" aria-controls="dev" role="tab" data-toggle="tab">DEV</a></li>
    <?php } ?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
  
  <?php if(DEBUG==true){ ?>
  <!-- DEBUG -->
    <div role="tabpanel" class="tab-pane" id="dev">
    	<div class="row">
    		<div class="col-md-12">
                
                <h2 class="text-center">Variables de sesión</h2>
                <pre id="sesion-monitor">
                <?php
                print_r($_SESSION);
                ?>
                </pre>
            </div>
    	</div>
    </div>
    <?php } ?>
    
    <!-- PERFIL DE USUARIO -->
    <div role="tabpanel" class="tab-pane" id="perfil">
    	<div class="row">
    		<div class="col-md-12">
                
                <h2 class="text-center">Perfil de usuario</h2>
                <?php include './views/Dashboard/perfil.php'; ?>
            </div>
    	</div>
    </div>
    <!-- PRIMER ANÁLISIS GRÁFICOS DE DONA / MARKET SHARE -->
    <div role="tabpanel" class="tab-pane active" id="market-share">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>Participación de mercado (market share)</h2>
            </div>
        </div>
        
        <div class="row equal-height-panels">
            <div class="col-md-6">
                <div class="panel panel-default" id="morris-1-panel">
                  <div class="panel-heading">
                    <h3 class="panel-title">Periodo anterior</h3>
                  </div>
                  <div class="panel-body">
                    <div id="morris-1-fechas" class="text-center"></div>
                    <div class="row">
                        <div class="col-md-7 vcenter">
                            <div id="morris-1" class="redibuja-morris"></div>
                        </div><!--
                        --><div class="col-md-5 vcenter">
                            <div class="col-md-12" id="morris-1-leyenda">
                                <ul class="list-unstyled">
                                    <li>No hay datos</li>
                                </ul>
                            </div>
                        </div>
                    
                    </div>
                    <div class="well text-center">
                        <div id="morris-1-etiquetas"></div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default" id="morris-2-panel">
                  <div class="panel-heading">
                    <h3 class="panel-title">Periodo seleccionado</h3>
                  </div>
                  <div class="panel-body">
                    <div id="morris-2-fechas" class="text-center"></div>
                    <div class="row">
                    	<div class="col-md-7 vcenter">
                            <div id="morris-2" class="redibuja-morris"></div>
                        </div><!--
                    	--><div class="col-md-5 vcenter">
                            <div class="col-md-12" id="morris-2-leyenda">
                                <ul class="list-unstyled">
                                    <li>No hay datos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="well text-center">
                        <div id="morris-2-etiquetas"></div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        
        <div class="row">
          <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="dataTable_wrapper dw1">
                            <table class="table table-striped table-bordered table-hover dataTables-activar display" id="marketshare1"></table>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="dataTable_wrapper dw2">
                            <table class="table table-striped table-bordered table-hover dataTables-activar display" id="marketshare2"></table>
                        </div>
                    </div>
                </div>
        
          </div>
        </div>
    </div>
    <!-- SEGUNDO ANÁLISIS: GRÁFICO DE LÍNEAS / VENTAS POR DÍA -->
    <div role="tabpanel" class="tab-pane" id="venta-por-dia">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>Ventas por día contra competidores</h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default" id="morris-3-panel">
                  <div class="panel-heading">
                    <h3 class="panel-title">Periodo anterior</h3>
                  </div>
                  <div class="panel-body">
                    <div id="morris-3-fechas" class="text-center"></div>
                    <div id="morris-3" class="redibuja-morris"></div>
                  </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default" id="morris-4-panel">
                  <div class="panel-heading">
                    <h3 class="panel-title">Periodo seleccionado</h3>
                  </div>
                  <div class="panel-body">
                    <div id="morris-4-fechas" class="text-center"></div>
                    <div id="morris-4" class="redibuja-morris"></div>
                  </div>
                </div>
            </div>
        </div>
        
        <div class="row">
          <div class="col-md-12">
                <div class="row" id="histograma">
                </div>
        
          </div>
        </div>
        
        <div class="row">
        	<div class="col-md-12">
            
                <div class="panel panel-default" id="morris-4-panel">
                  <div class="panel-heading">
                    <h3 class="panel-title">Comparación ventas periodo anterior con periodo actual, por proveedor</h3>
                  </div>
                  <div class="panel-body">
                    <div id="morris-barras-fechas" class="text-center"></div>
                    <div id="morris-barras"></div>
                    <div class="well well-sm">
                        <span id="morris-barras-leyenda" class="legend-bar"></span>
                    </div>
                  </div>
                </div>
        	</div>
        </div>
        <div class="row">
        	<div class="col-md-12">
                <div id="morris-barras-tabla"></div>
            </div>
        </div>
        
    </div>
    <!-- TERCER ANÁLISIS: COMPARATIVO POR PRODUCTOS -->
    <div role="tabpanel" class="tab-pane" id="comparativo">
        <div class="row">
        	<div class="col-md-12 text-center">
        		<h2>Comparativo por proveedor, por producto</h2>
        	</div>
            <div class="col-md-6">
            	<div class="panel panel-default">
            		<div class="panel-heading">
            			<div class="panel-title">Proveedor A</div>
            		</div>
            		<div class="panel-body">
                        <form action="" class="comparativoDropdown">
                            <div class="form-group">
                                <label for="comparativoDropdown1">Seleccione uno de la lista</label>
                                <select name="comparativoDropdown1" id="comparativoDropdown1" class="form-control" disabled>
                                <option>Seleccione un filtro primero</option>
                                <!--generado por AJAX-->
                                </select>
                                <div class="col-md-7 vcenter">
                                    <div id="morris-comparacion-1" class="redibuja-morris"></div>
                                </div><!--
                                --><div class="col-md-5 vcenter">
                                    <div class="col-md-12" id="morris-comparacion-1-leyenda">
                                        <ul class="list-unstyled">
                                            <li>No hay datos</li>
                                        </ul>
                                    </div>
                                </div>
                    
                                <div class="well text-center">
                                    <div id="morris-comparacion-1-etiquetas"></div>
                                </div>
                           </div>
                        </form>
                    </div>
            	</div>
            </div>
            <div class="col-md-6">
            	<div class="panel panel-default">
            		<div class="panel-heading">
            			<div class="panel-title">Proveedor B</div>
            		</div>
            		<div class="panel-body">
                        <form action="" class="comparativoDropdown">
                            <div class="form-group">
                                <label for="comparativoDropdown2">Seleccione uno de la lista</label>
                                <select name="comparativoDropdown2" id="comparativoDropdown2" class="form-control" disabled>
                                	<option>Seleccione un filtro primero</option>
                                <!--generado por AJAX-->
                                </select>
                                <div class="col-md-7 vcenter">
                                    <div id="morris-comparacion-2" class="redibuja-morris"></div>
                                </div><!--
                                --><div class="col-md-5 vcenter">
                                    <div class="col-md-12" id="morris-comparacion-2-leyenda">
                                        <ul class="list-unstyled">
                                            <li>No hay datos</li>
                                        </ul>
                                    </div>
                                </div>
                    
                                <div class="well text-center">
                                    <div id="morris-comparacion-2-etiquetas"></div>
                                </div>
                            </div>
                        </form>
                    </div>
            	</div>
            </div>
        </div>
        <div class="row">
        	<div class="col-md-6">
        		<table class="table table-striped table-bordered table-hover dataTables-activar display" id="comparativo-table-1">
        		</table>
        	</div>
        	<div class="col-md-6">
        		<table class="table table-striped table-bordered table-hover dataTables-activar display" id="comparativo-table-2">
        		</table>
        	</div>
        </div>
    </div>
  </div>

</div>



<?php require './public/php/Dashboard_footer.php'; ?>

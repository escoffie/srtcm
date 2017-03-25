<div id="tour-filtro-01">
<form action="Dashboard/histograma" method="post" id="gui_form_filtro">
      <div class="form-group">
        <h3 class="letrasFiltro">Fecha</h3>
        <input type="text" id="rangodefechas" name="rangodefechas" class="form-control">
      </div>
      <?php
        if(isset($_SESSION['filtro']['periodo'])){
            if($_SESSION['filtro']['periodo']==1){
                $checked1='checked';
                $checked2='';
            } else if($_SESSION['filtro']['periodo']==2){
                $checked1='';
                $checked2='checked';
            }
        } else {
            $checked1='';
            $checked2='checked';
        }
      ?>
      <div class="form-group">
        <div class="radio">
            <label>
              <input type="radio" name="periodo" id="periodo1" value="1" <?php echo $checked1; ?>> Año anterior
            </label>
        </div>
        <div class="radio">
            <label>
              <input type="radio" name="periodo" id="periodo2" value="2" <?php echo $checked2; ?>> Periodo anterior
            </label>
        </div>
      </div>
    
        <!--<div class="form-group" id="tour-sucursales">
            <h3>Sucursales</h3>
            <php
            foreach($filtroSucursales as $sucursal){
                if(isset($_SESSION['filtro']['filtro_sucursales']) and in_array($sucursal['codigo_sucursal_suc'], $_SESSION['filtro']['filtro_sucursales'])){
                    $checked='checked';
                } else {
                    $checked='';
                }
                $tooltip='';
                $disabled='';
                if($_SESSION['usuario']['codigo_pro']>0){
                    $tooltip_color='danger';
                    if($sucursal['estatus_esp']==1){
                        if(isset($sucursal['id_niv'])){
                            if($sucursal['id_niv']==1) $tooltip_color='warning'; 
                            if($sucursal['id_niv']==2) $tooltip_color='success'; 
                            $disabled='';
                            $tooltip = '<span class="label label-'.$tooltip_color.'" data-toggle="tooltip" data-placement="right" title="'.$sucursal['descripcion_niv'].'">'.$sucursal['nombre_niv'].'</span>';
                        } else {
                            $disabled='';
                            $tooltip='';
                            $sucursal['id_niv']=1;
                        }
                    } else {
                        $disabled='disabled';
                        $checked='';
                        $tooltip = '<span class="label label-danger" data-toggle="tooltip" data-placement="right" title="Para habilitar acceso a los datos de esta sucursal, comuníquese con un ejecutivo">Sin acceso</span>';
                    }
                } else {
                    $sucursal['descripcion_niv']='';
                    $sucursal['estatus_esp']='';	
                }
                ?>
                <div class="checkbox" title="<php echo $sucursal['descripcion_niv']; ?>">
                  <label class="<php echo $disabled; ?>">
                    <input data-nivel="<php echo $sucursal['id_niv']; ?>" class="check_sucursales" <php echo $checked; ?> <php echo $disabled; ?> type="checkbox" name="filtro_sucursales[]" id="codigo_sucursal_suc_<php echo $sucursal['codigo_sucursal_suc']; ?>" value="<php echo $sucursal['codigo_sucursal_suc']; ?>">
                    <small><php echo $sucursal['nombre_suc']; ?>  <php echo $tooltip; ?></small>
                    </label>
          </div>
                <php
            }
            ?>
      </div>-->
      
      <!--title="php echo $sucursal['descripcion_niv']; ?>"-->
      <div class="form-group" id="tour-sucursales">
            <h3 class="letrasFiltro">Sucursales</h3>
            <div class="checkbox">
            <?php 
				$premiun = array();
				$basico = array();
				$sin_acceso = array();
				$todas_sucursales = array();
				
				foreach($filtroSucursales as $sucursal){
					if(isset($_SESSION['filtro']['filtro_sucursales']) and in_array($sucursal['codigo_sucursal_suc'], $_SESSION['filtro']['filtro_sucursales'])){
                    $checked='selected';
                } else {
                    $checked='';
                }
						
					if($_SESSION['usuario']['codigo_pro']>0){
							if($sucursal['estatus_esp']==1){
								if(isset($sucursal['id_niv'])){
									if($sucursal['id_niv']==2){
									array_push($premiun,$sucursal['codigo_sucursal_suc'].'-'.$sucursal['nombre_suc'].'-'.$sucursal['id_niv'].'-'.$checked);
									}if($sucursal['id_niv']==1){
										array_push($basico,$sucursal['codigo_sucursal_suc'].'-'.$sucursal['nombre_suc'].'-'.$sucursal['id_niv'].'-'.$checked);
										}
								}
							}else{
								array_push($sin_acceso,$sucursal['codigo_sucursal_suc'].'-'.$sucursal['nombre_suc'].'-'.$sucursal['id_niv'].'-'.$checked);
								}
						}else{
							array_push($todas_sucursales,$sucursal['codigo_sucursal_suc'].'-'.$sucursal['nombre_suc']);
							}
		
					}
					
										
              ?>
                <select class="selectpicker" multiple title="Seleccione" data-actions-box="true" name="filtro_sucursales[]" data-size="6">
                <?php 
                if($_SESSION['usuario']['codigo_pro']>0){
			    ?>
                    <optgroup label="Premiun" class="label label-success" <?php if(count($premiun) == 0){?> disabled <?php }?>>	
                    <?php
					if(count($premiun) > 0){
					$x = implode(",",$premiun);
					$y = explode(",",$x);
					for($a=0;$a<count($y);$a++){
						$z = explode("-",$y[$a]);
					?>
                        <option value="<?php echo $z[0]?>" data-nivel="<?php echo $z[2]; ?>" <?php echo $z[3]?>><?php echo $z[1];?></option>
                     <?php 
								}
						}else{
					?>
							<option value="">No hay sucursal asignada</option>
                    <?php
							}
					?>
                    </optgroup>
                    
                    <optgroup label="Básico" class="label label-warning" <?php if(count($basico) == 0){?> disabled <?php }?>>
                    <?php
					if(count($basico) > 0){
					$x2 = implode(",",$basico);
					$y2 = explode(",",$x2);
					for($a2=0;$a2<count($y2);$a2++){
						$z2 = explode("-",$y2[$a2]);
					?>
                       	<option value="<?php echo $z2[0]?>" data-nivel="<?php echo $z2[2]; ?>" <?php echo $z2[3]?>><?php echo $z2[1];?></option>
                    <?php 
								}
						}else{
					?>
							<option value="">No hay sucursal asignada</option>
                    <?php
							}
					?>
                    </optgroup>
                    
                    <optgroup label="Sin Acceso" class="label label-danger" disabled>
                    <?php
					if(count($sin_acceso) > 0){
					$x3 = implode(",",$sin_acceso);
					$y3 = explode(",",$x3);
					for($a3=0;$a3<count($y3);$a3++){
						$z3 = explode("-",$y3[$a3]);
					?>
                        <option value="<?php echo $z3[0]?>"><?php echo $z3[1];?></option>
                    <?php 
								}
						}else{
					?>
							<option value="">No hay sucursal asignada</option>
                    <?php
							}
					?>
                    </optgroup>
                <?php 
				}else{
					if(count($todas_sucursales) > 0){
					$x4 = implode(",",$todas_sucursales);
					$y4 = explode(",",$x4);
					for($a4=0;$a4<count($y4);$a4++){
						$z4 = explode("-",$y4[$a4]);
				?>
                	 <option value="<?php echo $z4[0]?>"><?php echo $z4[1];?></option>
                    <?php 
								}
						}else{
					?>
							<option value="">No hay sucursal asignada</option>
                    <?php
					}
				}
				?>
                </select>
          	</div>   
      </div>
        
        <div class="form-group" <?PHP if($_SESSION['usuario']['codigo_pro']>0){ ?>style="display:none;"<?php }?>>
        <h3 class="letrasFiltro">Familias</h3>
          <select name="codigo_fam" id="codigo_fam" class="form-control">
            <option value="0"> Seleccione una</option>
            <?php
            foreach($filtroFamilias as $familia){
                if(isset($_SESSION['filtro']['codigo_fam']) and $familia['codigo_fam'] == $_SESSION['filtro']['codigo_fam']){
                    $checked='selected';
                } else {
                    $checked='';
                }
                ?>
               <option <?php echo $checked; ?> value="<?php echo $familia['codigo_fam']; ?>"> <?php echo $familia['nombre_fam']; ?></option>
                <?php
            }
            ?>
            </select>
            
      </div>
		 
            
        <div class="form-group">
            <h3 class="letrasFiltro">Categor&iacute;as</h3>
          <select name="codigo_sub" id="codigo_sub" class="form-control">
            <option value=""> Seleccione primero una familia</option>
          </select>
      </div>
     
      <input type="hidden" value="<?php echo $_SESSION['usuario']['id_niv']; ?>" id="id_niv" name="id_niv">
      
      <button type="submit" class="btn btn-danger" id="gui_btn_filtro"><i class="fa fa-filter"></i> Filtrar</button>
      <a href="<?php echo URL.'Dashboard/excel'; ?>" class="btn btn-danger" id="gui_btn_exportar" style="display:none;"><i class="fa fa-file-excel-o"></i> Exportar</a>
    
  
</form>

</div>
<script>
var filtros = {
	<?php
	if(isset($_SESSION['filtro']['rangodefechas'])){
		echo 'rangodefechas:"'.$_SESSION['filtro']['rangodefechas'].'"';
	}
	?>

};
</script>

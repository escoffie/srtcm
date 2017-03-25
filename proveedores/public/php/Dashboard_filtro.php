<div id="tour-filtro-01">

<form action="Dashboard/histograma" method="post" id="gui_form_filtro">
      <div class="form-group">
        <h3>Fecha</h3>
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
    
        <div class="form-group" id="tour-sucursales">
            <h3>Sucursales</h3>
            <?php
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
                <div class="checkbox" title="<?php echo $sucursal['descripcion_niv']; ?>">
                  <label class="<?php echo $disabled; ?>">
                    <input data-nivel="<?php echo $sucursal['id_niv']; ?>" class="check_sucursales" <?php echo $checked; ?> <?php echo $disabled; ?> type="checkbox" name="filtro_sucursales[]" id="codigo_sucursal_suc_<?php echo $sucursal['codigo_sucursal_suc']; ?>" value="<?php echo $sucursal['codigo_sucursal_suc']; ?>">
                    <small><?php echo $sucursal['nombre_suc']; ?>  <?php echo $tooltip; ?></small>
                    </label>
          </div>
                <?php
            }
            ?>
      </div>
        
        <div class="form-group">
            <h3>Familias</h3>
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
            <h3>Subfamilias</h3>
          <select name="codigo_sub" id="codigo_sub" class="form-control">
            <option value=""> Seleccione primero una familia</option>
          </select>
      </div>
      
      <input type="hidden" value="<?php echo $_SESSION['usuario']['id_niv']; ?>" id="id_niv" name="id_niv">
      
      <button type="submit" class="btn btn-success" id="gui_btn_filtro"><i class="fa fa-filter"></i> Filtrar</button>
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

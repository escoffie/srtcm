<?php
if($_SESSION['usuario']['direccion_pro']=='' or $_SESSION['usuario']['telefono_pro']=='' or $_SESSION['usuario']['email_pro']==''){
?>
<div class="alert alert-warning alert-dismissible" role="alert" id="alert-perfil">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<p><strong>Importante:</strong> Por favor, complete su información de perfil, y modifique su contraseña por una que incluya mayúsculas, minúsculas, números y signos.</p>
</div>
<?php
}
?>
<form action="<?php echo URL; ?>Dashboard/perfilUpdate" method="post" id="perfilUpdate_form">
    <div class="row">
        <div class="col-md-4" id="tour-perfil-01">
            
            <div class="form-group">
                <label for="codigo_pro">Código Interno</label>
                <input readonly type="text" class="form-control" id="codigo_pro" name="codigo_pro" value="<?php echo $perfil['codigo_pro']; ?>">
            </div>
            <div class="form-group">
                <label for="rfc_pro">RFC</label>
                <input readonly type="text" class="form-control" id="rfc_pro" name="rfc_pro" value="<?php echo $perfil['rfc_pro']; ?>">
            </div>
            <div class="form-group">
                <label for="razonsocial_pro">Razón Social</label>
                <input readonly type="text" class="form-control" id="razonsocial_pro" name="razonsocial_pro" value="<?php echo $perfil['razonsocial_pro']; ?>">
            </div>
                
        </div>
        <div class="col-md-4" id="tour-perfil-02">
            <div class="form-group">
                <label for="direccion_pro">Dirección</label>
                <input type="text" class="form-control" id="direccion_pro" name="direccion_pro" value="<?php echo $perfil['direccion_pro']; ?>">
            </div>
            <div class="form-group">
                <label for="telefono_pro">Teléfono</label>
                <input type="text" class="form-control" id="telefono_pro" name="telefono_pro" value="<?php echo $perfil['telefono_pro']; ?>">
            </div>
            <div class="form-group">
                <label for="email_pro">Email</label>
                <input type="text" class="form-control" id="email_pro" name="email_pro" value="<?php echo $perfil['email_pro']; ?>">
            </div>
        
        </div>
        <div class="col-md-4" id="tour-perfil-03">
            <div class="form-group">
                <label for="pw_pro">Contraseña</label>
                <input type="text" class="form-control" id="pw_pro" name="pw_pro">
            </div>
            <div class="form-group">
                <label for="pw_pro2">Repetir contraseña</label>
                <input type="text" class="form-control" id="pw_pro2" name="pw_pro2">
            </div>
            <div class="form-group">
                <label for="">&nbsp;</label>
                <button class="form-control btn btn-success" type="submit" id="perfilUpdate_btn">Guardar</button>            
            </div>
        </div>
    </div>
    <input type="hidden" name="id_rol" value="<?php echo $_SESSION['usuario']['id_rol']; ?>">
    <input type="hidden" name="codigo_pro" value="<?php echo $_SESSION['usuario']['codigo_pro']; ?>">
</form>

			<div class="container">
            	<div class="row">
                	<div class="col-sm-4 col-sm-offset-4">
                        <header class="text-center">
                            <img src="<?php echo IMG; ?>logo.png" alt="<?php echo CL_NOMBRE; ?>" class="img-responsive">
                            <h1>Iniciar sesión</h1>
                        </header>
                    </div>
                </div>
            	<div class="row">
                	<div class="col-sm-4 col-sm-offset-4">
						<div class="formWrapper" id="signInForm">
							<form action="<?php echo URL; ?>User/signIn/1" name="signIn" method="post" id="login">
								<input type="hidden" name="r" id="r_us" value="<?php if(isset($_GET['r'])) echo htmlspecialchars($_GET['r']); ?>">
								<div class="form-group"><input class="form-control" type="text" name="usuario" id="usuario" placeholder="Tu correo" required></div>
								<div class="form-group"><input class="form-control" type="password" name="pw_pro" id="pw_pro" placeholder="Contraseña" required></div>
								<div class="form-group"><input class="form-control btn btn-success" type="submit" id="signInSubmit" name="signInSubmit" value="Entrar"></div>
								<!--<div id="signUpToggler">Regístrate</div>
								<div id="recordarToggler">Recordar contraseña</div>-->
							</form>
						</div>
                    </div>
                </div>
            </div>

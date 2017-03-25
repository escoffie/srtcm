	<div class="modalDialog" id="quiero-ahorrar">
		<div>
			<a href="#" title="Cerrar" class="close">X</a>
			

			<section id="paso1">
				<form action="<?php echo URL;?>Index/index">
					<header class="text-center">
						<h1>¿Cuánto quieres ahorrar?</h1>
						<p>En Ecopulse recibirás atención personalizada. ¡Justo lo que necesitas!</p>
					</header>
					<main>
						<div class="form-group">
							<div class="row">
								<div class="col-xs-6 col-sm-4">
									<div class="radio"><label><input class="al-cambiar" checked type="radio" name="tipo" id="tipo1" value="Residencial">Residencial</label></div>
								</div>
								<div class="col-xs-6 col-sm-4">
									<div class="radio"><label><input class="al-cambiar" type="radio" name="tipo" id="tipo2" value="Comercial">Comercial</label></div>
								</div>
								<div class="col-xs-6 col-sm-4">
									<div class="radio"><label><input class="al-cambiar" type="radio" name="tipo" id="tipo3" value="Industrial">Industrial</label></div>
								</div>								
							</div>
						</div>
						<div id="no-industrial" class="form-group">
							<div class="row">
								<div class="col-xs-6">
									<select name="frecuenciapago_cal" id="frecuenciapago_cal" class="form-control">
										<option value="1">Pago bimestral</option>
										<option value="2">Pago mensual</option>
									</select>
								</div>
								<div class="col-xs-6">
									<div class="input-group">
										<div class="input-group-addon">$</div>
										<input type="number" name="monto_cal" id="monto_cal" class="form-control" placeholder="0000" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
									//Como está en /libs/ se incluye dinámicamente (autoload)
									$estados = new Estados();
									$lista = $estados->seleccionarEstados(1);
									$selectOptions = '';
									foreach ($lista as $key => $value) {
										$selectOptions .= "<option value=\"$value[id_es]\">$value[estado_es]</option>";
									}
									?>
									<select name="id_es" id="id_es" class="form-control al-cambiar-select">
										<option disabled selected>Tu estado</option>
										<?php echo $selectOptions; ?>
									</select>
								</div>
							</div>
						</div>
						<div id="industrial" class="form-group">
							<div class="row">
								<div class="col-md-12">
									<a href="http://www.ecopulse.mx" target="_blank">
										<div class="recuadro">
											<div class="media">
												<div class="media-left">
													<img class="media-object" src="<?php echo IMGL; ?>Landing_Boton_Industria.png" width="64" alt="Industrial">
												</div>
												<div class="media-body text-left">
													<h4 class="media-heading">¿Proyectos industriales?</h4>
													<p>Tenemos una solución para ti</p>
												</div>
												<div class="media-right media-middle">
													<p><span class="fa fa-angle-right fa-2x"></span></p>
												</div>
											</div>
										</div>
									</a>
								</div>
							</div>
						</div>
						
						<div class="row hidden-xs">
							<div class="col-md-12 text-center">
								<p><strong>¿No estamos en tu estado?</strong></p>
								<p>Ecopulse cerca de ti próximamente</p>
							</div>
							<div class="col-md-12">
								<?php
								$next = $estados->seleccionarEstados(-1);
								if(sizeof($next)>0) {
									$li ='';
									foreach ($next as $key => $value) {
										$li .="<li><span class=\"fa-li fa fa-map-marker\"></span>$value[estado_es]</li>";
									}
									?>
									<div class="multiColumn">
										<ul class="fa-ul">
											<?php echo $li; ?>
										</ul>
									</div>
									<?php
								}
								?>
							</div>
						</div>
						
					</main>
					<footer class="text-center" id="calcular">
                        <button type="submit" >Calcular</button>
					</footer>
				</form>
			</section>

			<section id="paso2">			
				<header class="text-center">
					<h1>Resultado</h1>
				</header>

				<div class="container-fluid">

					<div class="row">
						<div class="col-sm-6">
							<p class="museo500">Comienza a producir tu propia energía con las mejores opciones de ahorro.</p>
							<div class="row">
								<div class="col-xs-4">
									<figure class="text-center">
										<img src="<?php echo IMGL; ?>Landing_Calc_2.png" alt="Consumo" class="center-block">
										<figcaption><div><span id="consumo" class="contar">1888</span>*</div><div class="leyenda">Consumo<br>(pago normal)</div></figcaption>
									</figure>
								</div>
								<div class="col-xs-4">
									<figure class="text-center">
										<img src="<?php echo IMGL; ?>Landing_Calc_1.png" alt="Producción" class="center-block">
										<figcaption><div><span id="ahorro" class="contar">2000</span>*</div><div class="leyenda">Producción<br>(tu ahorro)</div></figcaption>
									</figure>
								</div>
								<div class="col-xs-4">
									<figure class="text-center">
										<img src="<?php echo IMGL; ?>Landing_Calc_3.png" alt="Ahorro" class="center-block">
										<figcaption><div><span id="produccion" class="contar">5678</span>*</div><div class="leyenda">Nuevo pago<br>(¡Genial!)</div></figcaption>
									</figure>
								</div>
							</div>
							<ul class="margen-doble definiciones hidden-xs">
								<li>Consumo: Energía que tu casa o negocio demanda</li>
								<li>Producción: Energía que los paneles solares generan, es decir, tu ahorro</li>
								<li>Nuevo pago: Resultado de la producción, menos el consumo</li>
							</ul>
	                        <div class="row">
	                        	<div class="col-xs-4">
	                                <p><button type="button" id="cal_anual" class="btn btn-block btn-default active" value="1">Anual</button></p>                            
	                            </div>
	                        	<div class="col-xs-4">                            
	                                <p><button type="button" id="cal_bimestral" class="btn btn-block btn-default" value="6">Bimestral</button></p>
	                            </div>
	                        	<div class="col-xs-4">                            
	                                <p><button type="button" id="cal_mensual" class="btn btn-block btn-default" value="12">Mensual</button></p>
	                            </div>
	                        </div>
							<p class="museo500 text-center font-naranja margen-doble">¡A mayor producción solar y mismo consumo, <strong>tendrás más ahorro</strong>!</p>
							<p><small>* Valores aproximados</small></p>
						</div>
						<div class="col-sm-6 raya-a-la-izquierda">


							<p class="museo500">Regístrate para conocer todos los beneficios que Ecopulse te ofrece:</p>
					
							<div class="formWrapper" id="signUpForm">
								<form action="<?php echo URL; ?>User/signUp" name="signUp" id="signUpForm" method="post" autocomplete="off">
									
	                        		<input type="hidden" name="estado_es" id="estado_es" value="null">

	                                <div class="form-group" id="organizacion"><input class="form-control" type="text" name="organizacion_us" id="organizacion_us" placeholder="Tu organización"></div>
	                                
	                                <div class="form-group"><input class="form-control" type="text" data-nombre="Nombre" name="nombre_us" id="nombre_us" placeholder="Tu nombre" required></div>
									<div class="form-group"><input class="form-control" type="tel" data-nombre="Teléfono" name="telefono_us" id="telefono_us" placeholder="(999) 123 4567" required></div>
									<div class="form-group"><input class="form-control" type="email" data-nombre="Correo electrónico" name="correo_us" id="correo_us" placeholder="Tu correo" required></div>
									<div class="form-group"><input class="form-control" type="password" data-nombre="Contraseña" name="contrasena_us" id="contrasena_us" placeholder="Contraseña" required></div>
									<div class="form-group"><input class="form-control" type="password" data-nombre="Confirmar contraseña" name="contrasena2_us" id="contrasena2_us" placeholder="Confirmar contraseña" required></div>
									<div class="form-group">
										<button class="btn btn-warning" type="submit" id="signUpSubmit" name="signUpSubmit"><span class="fa fa-user-plus"></span> Regístrate</button>
										<button class="btn btn-default" type="button" id="volver" name="volver"><span class="fa fa-calculator"></span> Volver a calcular</button>
	                                </div>
	                                <div class="form-group">Al hacer clic en el botón "Regístrate" estás aceptando nuestro <a href="Index/privacidad" class="privacidad">Aviso de privacidad <span class="fa fa-external-link"></span></a></div>
									<!--<div id="signInToggler">Accede</div>-->
								</form>
							</div>



						</div>
					</div>

				</div>

			</section>

			<section id="paso3">
				<div class="row">
					<div class="col-md-12 text-center">
						<h1 class="museo700 font-naranja">¡Muchas gracias!</h1>
						<h3 class="museo500">Has dado el primer paso para comenzar a ahorrar en tu recibo de energía</h3>
						<p>En unos minutos recibirás un correo electrónico desde el que podrás acceder a más detalles sobre cómo ahorrar.</p>
						<p><a href="#close" class="btn btn-warning">Cerrar</a></p>
					</div>
				</div>
			</section>

		</div>
	</div>

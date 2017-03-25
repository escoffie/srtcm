<?php

class Model {

	function __construct(){
		//Creo que no se necesita incluir libs/MySQLiManager.php por un pedazo de código que está en /index.php
		$this->db = new MySQLiManager(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	}

}
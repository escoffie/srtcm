<?php

class Session {

	static function init(){
		@session_start();
	}

	static function destroy(){
		session_destroy();
	}

	static function getValue($var) {
		if(isset($_SESSION[$var])) return $_SESSION[$var];
		else return false;
	}

	static function setValue($var, $val){
		$_SESSION[$var] = $val;
	}

	static function unsetValue($var){
		if(isset($_SESSION[$var])){
			unset($_SESSION[$var]);
		}
	}

	static function exists(){
		if(sizeof($_SESSION)>0){
			return true;
		} else {
			false;
		}
	}

}
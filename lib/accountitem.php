<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Accountitem implements \Truemodelitem {
	private $model;
	
	const FIELD_ID = "id";
	const FIELD_NAME = "name";
	const FIELD_EMAIL = "email";
	const FIELD_PASSWORD = "password";
	
	const DEFAULT_ID = 0;
	const DEFAULT_NAME = "";
	const DEFAULT_EMAIL = "";
	const DEFAULT_PASSWORD = "";
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function __set($name, $value) {
		switch($name) {
			// Integers
			case self::FIELD_ID:
				$this->$name = ($value === NULL) ? NULL : intval($value);
				break;
			
			// String Rest
			default:
				$this->$name = $value;
				break;
		}
	}
	
	public function get_id()       {return $this->id;}
	public function get_name() {return $this->name;}
	public function get_email()   {return $this->email;}
	public function get_password()    {return $this->password;}
	
	public function set_password($password) {$this->password = $password;}
	
	public function verify_password($password, $update = true) {
		if(password_verify($password, $this->get_password())) {
			if(password_needs_rehash($this->get_password(),  Accounts::HASH_ALGO)) {
				$this->set_password(Accounts::hash());
			}
			
			return true;
		}
		else {
			return false;
		}
	}
}
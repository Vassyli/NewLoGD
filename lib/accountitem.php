<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
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
	
	public function __construct(\Model $model) {
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
	
	public function getId()       {return $this->id;}
	public function getName() {return $this->name;}
	public function getEmail()   {return $this->email;}
	public function getPassword()    {return $this->password;}
	
	public function setPassword($password) {$this->password = $password;}
	
	public function verifyPassword($password, $update = true) {
		if(password_verify($password, $this->getPassword())) {
			if(password_needs_rehash($this->getPassword(),  Accounts::HASH_ALGO)) {
				$this->setPassword(Accounts::hash());
			}
			
			return true;
		}
		else {
			return false;
		}
	}
}
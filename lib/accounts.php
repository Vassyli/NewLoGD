<?php

class Accounts implements Submodel {
	use lazy;
	
	private $model;
	
	const HASH_ALGO = PASSWORD_DEFAULT;
	const HASH_COST = 10;
	
	public function __construct($model) {
		$this->model = $model;
		$this->set_lazy_keys(array("id", "name"));
	}
	
	public function create($name, $password, $email) {
		$query = $this->model->insertInto("accounts")
			->addFields("name", "password", "email", array("created-on", new \Query\SQLFunction("NOW")))
			->addValues($name, $this->hash($password), $email)
			->execute();
	}
	
	public function hash($inp) {
		if(self::HASH_ALGO == PASSWORD_BCRYPT) {
			return password_hash($inp, self::HASH_ALGO, array("cost" => self::HASH_COST));
		}
		else {	
			return password_hash($inp, self::HASH_ALGO);
		}
	}
	
	protected function check($field, $value) {
		$query = $this->model->from("accounts")->where($field, $value);
		
		if(count($query) > 0) {
			return false;
		}
		else {
			return true;
		}
	}
	
	public function check_name($name) {
		return $this->check("name", $name);
	}
	
	public function check_email($email) {
		return $this->check("email", $email);
	}
}
<?php

class Accounts implements Submodel {
	use lazy;
	
	private $model;
	private $active;
	
	const HASH_ALGO = PASSWORD_DEFAULT;
	const HASH_COST = 10;
	
	public function __construct(Model $model) {
		$this->model = $model;
		$this->set_lazy_keys(array("id", "name", "email"));
	}
	
	private function getby_uniquekey($key, $val) {
		if($this->has_lazy($key, $val)) {
			return $this->get_lazy($key, $val);
		}
		else {
			$result = $this->model->from("accounts")
				->where($key, $val)
				->where("locked", 0);
			
			if(count($result) > 0) {
				$row = $result->fetchObject("AccountItem", array($this->model));
				$this->set_lazy($row);
				return $row;
			}
			else {
				return false;
			}
		}
	}
	
	public function getby_id($id) {
		return $this->getby_uniquekey("id", $id);
	}
	
	public function getby_email($email) {
		return $this->getby_uniquekey("email", $email);
	}
	
	public function set_active($id) {
		$this->active = $id;
		$this->getby_id($id);
	}
	
	public function get_active() {
		return $this->getby_id($this->active);
	}
	
	public function create($name, $password, $email) {
		$query = $this->model->insertInto("accounts")
			->addFields("name", "password", "email", array("created-on", new \Query\SQLFunction("NOW")))
			->addValues($name, self::hash($password), $email)
			->execute();
	}
	
	public static function hash($inp) {
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
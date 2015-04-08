<?php

class Model {
	private $type = "";
	private $dbh = NULL;
	
	private $get = "";
	private $post = "";
	
	private $res_action = "";
	private $res_arguments = array();
	
	private $submodels = array();
	
	const DB_MYSQL = "mysql";
	
	public function __construct(array $get, array $post) {
		$this->get = $get;
		$this->post = $post;
		
		$this->parse_qs();
		
		Include LOGD_DBCONFIG;
		
		$this->set_type($DB_TYPE);
		$this->prefix = $DB_PREFIX;
		$this->connect($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS);
	}
	
	private function set_type($type) {
		switch($type) {
			case self::DB_MYSQL: $this->type = self::DB_MYSQL; break;
			default: throw new Exception(sprintf("ModelError: Unknown DB Type: %s", $type));
		}
	}
	
	protected function connect($host, $name, $user, $pass) {
		$dsn = sprintf("mysql:dbname=%s;host=%s;charset=utf8", $name, $host);
		try {
			$this->dbh = new PDO($dsn, $user, $pass);
		}
		catch(PDOException $e) {
			die("Error: Connection to Database was not possible.");
		}
	}
	
	public function add_prefix($table) {
		return $this->prefix . $table;
	}
	
	public function get_dbh() {
		return $this->dbh;
	}
	
	public function from($table, $id = NULL) {
		$query = new \Query\Select($this, $table);	
		if($id !== NULL) {
			$query->where("id", $id);
		}
		
		return $query;
	}
	
	public function insertInto($table) {
		$query = new \Query\InsertInto($this, $table);
		return $query;
	}
	
	public function get($submodel) {
		if(!isset($this->submodels[$submodel])) {
			$this->submodels[$submodel] = new $submodel($this);
		}
		
		return $this->submodels[$submodel];
	}
	
	private function parse_qs() {
		$parts = explode("/", $this->get['qs']);
		$this->res_action = array_shift($parts);
		
		if(empty($this->res_action)) {
			$this->res_action = "main";
		}
		
		if(count($parts) > 0) {
			$this->res_arguments = $parts;
		}
	}
	
	public function get_res_action() {
		return $this->res_action;
	}
	
	public function get_res_arguments() {
		return $this->res_arguments;
	}
	
	public function get_postvalue($key) {
		if(!empty($this->post[$key])) {
			return $this->post[$key];
		}
		else {
			return "";
		}
	}
	
	public function get_postarray() {
		return $this->post;
	}
}
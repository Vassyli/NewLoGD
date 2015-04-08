<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */
 /**
  * The Model.
  *
  * This class manages all information used in this application, both static and dynamic.
  */
class Model {
	/** @var string The database type (a constant self::DB_*) */
	private $type = "";
	/** @var \PDO Contains a reference to the \PDO class */
	private $dbh = NULL;
	
	/** @var array A copy of $_GET */
	private $get = array();
	/** @var array A copy of $_POST */
	private $post = array();
	
	/** @var string The requested action */
	private $res_action = "";
	/** @var array additional page request arguments */
	private $res_arguments = array();
	
	/** @var array A list of loaded submodels (modelname => \Submodel $submodel) */
	private $submodels = array();
	
	/** @var string An internal identifier for the DB-Type MySQL */
	const DB_MYSQL = "mysql";
	
	/**
	 * The constructor.
	 *
	 * The constructor includes the file LOGD_DBCONFIG in order to set up the database connection
	 *
	 * @param array $get A copy of $_GET
	 * @param array $post A copy of $_POST
	 */
	public function __construct(array $get, array $post) {
		$this->get = $get;
		$this->post = $post;
		
		// Parse the query string from $_GET['qs']
		$this->parse_qs();
		
		// Get db informations
		Include LOGD_DBCONFIG;
		
		$this->set_type($DB_TYPE);                              // Set DB type
		$this->prefix = $DB_PREFIX;                             // Set prefix
		$this->connect($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS); // Establish DB connection
	}
	
	/**
	 * Sanitizes the database type in order to only allow supported types.
	 *
	 * @param string $type A identifier for the database type.
	 */
	private function set_type($type) {
		switch($type) {
			case self::DB_MYSQL: $this->type = self::DB_MYSQL; break;
			default: throw new Exception(sprintf("ModelError: Unknown DB Type: %s", $type));
		}
	}
	
	/**
	 * Connects to the database
	 *
	 * @param string $host Database-Hostname or IP
	 * @param string $name Name of the Database
	 * @param string $user Username 
	 * @param string $pass Password of the User
	 */
	protected function connect($host, $name, $user, $pass) {
		$dsn = sprintf("mysql:dbname=%s;host=%s;charset=utf8", $name, $host);
		try {
			$this->dbh = new PDO($dsn, $user, $pass);
		}
		catch(PDOException $e) {
			die("Error: Connection to Database was not possible.");
		}
	}
	
	/**
	 * Adds the defined prefix to a tablename
	 *
	 * This function adds a prefix to the tablename. It should never be called
	 * manually, since all Query-Classes are adding the prefix by themselves.
	 *
	 * @param string $table Tablename which has get a prefix added
	 * @return string The tablename with an added prefix
	 */
	public function add_prefix($table) {
		return $this->prefix . $table;
	}
	
	/**
	 * Returns the DB handler
	 *
	 * @return PDO The active DB Handler
	 */
	public function get_dbh() {
		return $this->dbh;
	}
	
	/**
	 * Starts a Select-Query from a given table
	 *
	 * @param string $table The Table-Name (without prefix)
	 * @param int|string $id If given, it restricts the query to the row with the id $id.
	 * @return \Query\Select An instance of the Select-Querybuilder.
	 */
	public function from($table, $id = NULL) {
		$query = new \Query\Select($this, $table);	
		if($id !== NULL) {
			$query->where("id", $id);
		}
		
		return $query;
	}
	
	/**
	 * Starts an Insert-Query into a given table
	 *
	 * @param string $table The Table-Name (without prefix)
	 * @return \Query\InsertInto An instance of the InsertInto-Querybuilder.
	 */
	public function insertInto($table) {
		$query = new \Query\InsertInto($this, $table);
		return $query;
	}
	
	/**
	 * Returns an instance of the requested Submodel
	 *
	 * @param string $submodel The Submodel's name.
	 * @return Submodel The instance of the requested Submodel.
	 * @throws Exception if the requested Submodel does not implement the interface Submodel
	 */
	public function get($submodel) {
		if(!isset($this->submodels[$submodel])) {
			if(in_array("submodel", class_implements($submodel))) {
				$this->submodels[$submodel] = new $submodel($this);
			}
			else {
				throw new Exception("The requested Submodel does not implement the Submodel interface.");
			}
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
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
	
	/** @var string A basic content type used to request (html or json) */
	public $contenttype = "html";
	
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
		$this->parseQuerystring();
		
		// Get db informations
		Include LOGD_DBCONFIG;
		
		$this->setType($DB_TYPE);                              // Set DB type
		$this->prefix = $DB_PREFIX;                             // Set prefix
		$this->connect($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS); // Establish DB connection
	}
	
	/**
	 * Sanitizes the database type in order to only allow supported types.
	 *
	 * @param string $type A identifier for the database type.
	 */
	private function setType($type) {
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
		$dsn = sprintf("mysql:dbname=%s;host=%s;charset=utf8mb4", $name, $host);
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
	public function addPrefix($table) {
		return $this->prefix . $table;
	}
	
	/**
	 * Returns the DB handler
	 *
	 * @return \PDO The active DB Handler
	 */
	public function getDbh() {
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
	 * Starts an Update-Query for a given table.
	 *
	 * @param string $table The Table-Name (without prefix)
	 * @return \Query\Update An instance of the Update-Querybuilder.
	 */
	public function update($table) {
		$query = new \Query\Update($this, $table);
		return $query;
	}
    
    /**
	 * Starts a Delete-Query for a given table.
	 *
	 * @param string $table The Table-Name (without prefix)
	 * @return \Query\Delete An instance of the Delete-Querybuilder.
	 */
	public function delete($table) {
		$query = new \Query\Delete($this, $table);
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
		$submodel_name = "Submodel\\".strtolower($submodel);
		if(!isset($this->submodels[$submodel_name])) {
			if(in_array("Submodel\SubmodelInterface", class_implements($submodel_name))) {
				$this->submodels[$submodel_name] = new $submodel_name($this);
			}
			else {
				throw new Exception("The requested Submodel does not implement the Submodel interface.");
			}
		}
	
		return $this->submodels[$submodel_name];
	}
	
    /**
     * Parses the query-string contained in $_GET['qs'] (and in $this->get['qs'])
     */
	private function parseQuerystring() {
		$parts = explode("/", $this->get['qs']);
		$this->res_action = array_shift($parts);
		
		if(empty($this->res_action)) {
			$this->res_action = "main";
		}
		
		if(count($parts) > 0) {
			$this->res_arguments = $parts;
		}
		
		$this->contenttype = empty($this->get['type']) ? "html" : $this->get['type'];
		define("LOGD_CONTENTTYPE", $this->contenttype);
	}
	
    /**
     * Returns the action of the requested ressource
     * 
     * @return string Returns the requested action
     */
	public function getRessourceAction() {
		return $this->res_action;
	}
	
    /**
     * Returns additional arguments of the requested ressource
     * 
     * @return array Returns additional Arguments
     */
	public function getRessourceArguments() {
		return $this->res_arguments;
	}
	
    /**
     * Returns a POST-Value given by it's argument
     * 
     * @param string $key The requested key
     * @return string The value which belongs to $key
     */
	public function getPostvalue($key) {
		if(!empty($this->post[$key])) {
			return $this->post[$key];
		}
		else {
			return "";
		}
	}
	
    /*
     * Returns an array of all POST values
     * 
     * @return array POST-values
     */
	public function getPostarray() {
		return $this->post;
	}
}
<?php

namespace Query;

class Select extends Base {
	protected $model = NULL;
	protected $table = "";
	
	protected $fragments = array("SELECT" => array(), "WHERE" => array(), "GROUPBY" => array(), "ORDERBY" => array(), "LIMIT" => array());
	
	protected $is_executed = false;
	protected $result = NULL;
	
	public function __construct(\Model $model, $table) {
		$this->model = $model;
		$this->table = $table;
	}
	
	public function where($field = NULL, $value = "", $operator = self::OPERATOR_EQ) {
		if($field === NULL) {
			$this->fragments["WHERE"] = array();
			return $this;
		}
		
		array_push($this->fragments["WHERE"], array($field, $value, $operator));
		return $this;
	}
	
	public function select($field = NULL) {
		if($field === NULL) {
			$this->fragments["SELECT"] = array();
			return $this;
		}
	}
	
	public function orderby($field = NULL, $order = parent::ORDER_ASC, $inversion = false) {
		if($field === NULL) {
			$this->fragments["ORDERBY"] = array();
			return $this;
		}
		
		array_push($this->fragments["ORDERBY"], array($field, $order, $inversion));
		return $this;
	}
	
	private function execute_if_needed() {
		if($this->is_executed === false) {
			$this->is_executed = true;
			$this->result = $this->execute();
		}
	}
	
	public function fetch() {
		$this->execute_if_needed();
		
		if($this->result[0] == false) {
			throw new \Exception(vsprintf("DatabaseError [%s,%s]: %s", $this->result[1]->errorInfo()));
			return false;
		}
		else {
			return $this->result[1]->fetch(\PDO::FETCH_ASSOC);
		}
	}
	
	public function fetchObject($classname, array $arguments = array()) {
		$this->execute_if_needed();
		
		if($this->result[0] == false) {
			throw new \Exception(vsprintf("DatabaseError [%s,%s]: %s", $this->result[1]->errorInfo()));
			return false;
		}
		else {
			return $this->result[1]->fetchObject($classname, $arguments);
		}
	}
	
	protected function execute() {
		$ret = $this->build_query();
		
		$prepared = $this->model->get_dbh()->prepare($ret[0]);
		$result = $prepared->execute($ret[1]);
		
		return array($result, $prepared);
	}
	
	protected function build_query() {
		$query = "";
		$args = array();
		$table = $this->model->add_prefix($this->table);
		
		// SELECT 
		$query .= "SELECT ";
		if(empty($this->fragments["SELECT"])) {
			$query .= "* ";
		}
		else {
			$i = 0;
			foreach($this->fragments["SELECT"] as $field) {
				if($i > 0) {
					$query .= ", ";
				}
				$query .= sprintf("%s.%s", $table, $field);
				$i++;
			}
		}
		
		// FROM
		$query .= sprintf(" FROM %s", $table);
		
		// WHERE
		if(!empty($this->fragments["WHERE"])) {
			$query .= " WHERE ";
			
			$i = 0;
			foreach($this->fragments["WHERE"] as $clause) {
				if($i > 0) {
					$query .= " AND ";
				}
				$query .= sprintf("%s.%s %s %s", $this->model->add_prefix($this->table), $clause[0], $clause[2], ":".$clause[0]);
				$args[":".$clause[0]] = $clause[1];
				$i++;
			}
		}
		
		// ORDER BY
		if(!empty($this->fragments["ORDERBY"])) {
			$query .= " ORDER BY ";
			$i = 0;
			foreach($this->fragments["ORDERBY"] as $clause) {
				if($i > 0) {
					$query .= ", ";
				}
				if($clause[2] == true) {
					// Inverted query: field gets to -field, ASC gets DESC and DESC gets ASC.
					// Used to get NULL values at the end.
					$orderclause = ($clause[1] == self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC);
					$query .= sprintf("-%s.%s %s", $this->model->add_prefix($this->table), $clause[0], $orderclause);
				}
				else {
					$query .= sprintf("%s.%s %s", $this->model->add_prefix($this->table), $clause[0], $clause[1]);
				}
				$i++;
			}
		}
		
		return array($query, $args);
	}
}
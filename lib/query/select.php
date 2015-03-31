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
		}
		else {
			array_push($this->fragments["WHERE"], array(
				"field" => (is_array($field) ? $field[1] : $field),
				"table" => (is_array($field) ? $field[0] : $this->table),
				"value" => $value, 
				"operator" => $operator,
			));
		}
		
		return $this;
	}
	
	public function select($field = NULL, $alias = NULL) {
		if($field === NULL) {
			$this->fragments["SELECT"] = array();
		}
		else {
			array_push($this->fragments["SELECT"], array(
				"field" => (is_array($field) ? $field[1] : $field),
				"table" => (is_array($field) ? $field[0] : $this->table),
				"alias" => $alias,
			));
		}
		
		return $this;
	}
	
	public function orderby($field = NULL, $order = parent::ORDER_ASC, $inversion = false) {
		if($field === NULL) {
			$this->fragments["ORDERBY"] = array();
		}
		else {
			array_push($this->fragments["ORDERBY"], array(
				"field" => (is_array($field) ? $field[1] : $field),
				"table" => (is_array($field) ? $field[0] : $this->table),
				"order" => $order, 
				"inversion" => $inversion
			));
		}
		return $this;
	}
	
	public function orderby_condition($condition_field, $value, $operator, $true_field, $false_field) {	
		array_push($this->fragments["ORDERBY"], array(
			"conditional" => true, 
			"condition-field" => (is_array($condition_field) ? $condition_field[1] : $condition_field),
			"condition-table" => (is_array($condition_field) ? $condition_field[0] : $this->table),
			"condition-value" => $value, 
			"operator" => $operator, 
			"true-field" => $true_field, 
			"false-field" => $false_field
		));
		
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
		debug("<b>Query:</b>\n&lt;".$ret[0]."&gt;\n");
		
		$prepared = $this->model->get_dbh()->prepare($ret[0]);
		$result = $prepared->execute($ret[1]);
		
		return array($result, $prepared);
	}
	
	protected function build_query() {
		$query = "";
		$args = array();
		$table = $this->model->add_prefix($this->table);
		
		// SELECT 
		$query .= "SELECT\n\t";
		if(empty($this->fragments["SELECT"])) {
			if(empty($this->fragments["JOINS"])) {
				$query .= "*";
			}
			else {
				$query .= sprintf("`%s`.*", $table);
			}
		}
		else {
			$i = 0;
			foreach($this->fragments["SELECT"] as $fieldInfo) {
				if($i > 0) {
					$query .= ",\n\t";
				}
				
				if(empty($fieldInfo["alias"])) {
					$query .= sprintf("`%s`.`%s`", $this->model->add_prefix($fieldInfo["table"]), $fieldInfo["field"]);
				}
				else {
					$query .= sprintf("`%s`.`%s` AS \"%s\"", $this->model->add_prefix($fieldInfo["table"]), $fieldInfo["field"], $fieldInfo["alias"]);
				}
				
				$i++;
			}
		}
		
		// FROM
		$query .= sprintf("\nFROM `%s`", $table);
		
		// WHERE
		if(!empty($this->fragments["WHERE"])) {
			$query .= "\nWHERE ";
			
			$i = 0;
			foreach($this->fragments["WHERE"] as $clause) {
				if($i > 0) {
					$query .= " AND ";
				}
				
				if(is_null($clause["value"])) {
					if($clause["operator"] == self::OPERATOR_EQ) {
						$clause["operator"] = self::OPERATOR_IS;
					}
					elseif($clause["operator"] == self::OPERATOR_NEQ) {
						$clause["operator"] = self::OPERATOR_ISNOT;
					}
					
					$query .= sprintf("`%s`.`%s` %s NULL", $clause["table"], $clause["field"], $clause["operator"]);
				}
				else {
					$fieldvar = ":".$clause["table"]."_field_".$clause["field"];
					$query .= sprintf("`%s`.`%s` %s %s", $clause["table"], $clause["field"], $clause["operator"], $fieldvar);
					$args[$fieldvar] = $clause["value"];
				}
				
				$i++;
			}
		}
		
		// ORDER BY
		if(!empty($this->fragments["ORDERBY"])) {
			$query .= "\nORDER BY \n\t";
			$i = 0;
			foreach($this->fragments["ORDERBY"] as $clause) {
				if($i > 0) {
					$query .= ",\n\t";
				}
				
				if(empty($clause["conditional"])) {
					// Standard order-by
					if($clause["inversion"] == true) {
						// Inverted query: field gets to -field, ASC gets DESC and DESC gets ASC.
						// Used to get NULL values at the end.
						$query .= sprintf(
							"-`%s`.`%s` %s", 
							$clause["table"], 
							$clause["field"], 
							($clause["order"] == self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC)
						);
					}
					else {
						$query .= sprintf(
							"`%s`.`%s` %s", 
							$clause["table"], 
							$clause["field"],
							($clause["order"] == self::ORDER_ASC ? self::ORDER_ASC : self::ORDER_DESC)
						);
					}
				}
				else {
					// Conditional order-by
					if(is_null($clause["condition-value"])) {
						if($clause["operator"] == self::OPERATOR_EQ) {
							$clause["operator"] = self::OPERATOR_IS;
						}
						elseif($clause["operator"] == self::OPERATOR_NEQ) {
							$clause["operator"] = self::OPERATOR_ISNOT;
						}
						
						$orderclause = "IF(`%s`.`%s` %s NULL, `%s`, `%s`)";
						$query .= sprintf(
							$orderclause, 
							$clause["condition-table"],
							$clause["condition-field"], 
							$clause["operator"], 
							$clause["true-field"], 
							$clause["false-field"]
						);
					}
					else {
						$orderclause = "IF(`%s`.`%s` %s %s, `%s`, `%s`)";
						$fieldvar = ":conditional_".$clause["condition-table"]."_field_".$clause["condition-field"];
						$args[$fieldvar] = $clause["condition-value"];
						$query .= sprintf(
							$orderclause,
							$clause["condition-table"],
							$clause["condition-field"], 
							$clause["operator"], 
							$fieldvar,
							$clause["true-field"], 
							$clause["false-field"]
						);
					}
				}
				$i++;
			}
		}
		
		return array($query, $args);
	}
}
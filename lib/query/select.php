<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Query;

/**
 * Builds a SELECT database query
 */
class Select extends Base implements \Countable {
	protected $model = NULL;
	protected $table = "";
	
	protected $fragments = [
        "SELECT" => [], 
        "JOIN" => [], 
        "WHERE" => [], 
        "GROUPBY" => [], 
        "ORDERBY" => [], 
        "LIMIT" => []
    ];
	
	protected $result = NULL;
	
	protected $countable_column = "*";
	
	public function select($field = NULL, $alias = NULL) {
		if($field === NULL) {
			$this->fragments["SELECT"] = [];
		}
		else {
			array_push($this->fragments["SELECT"], [
                "field" => (is_array($field) ? $field[1] : $field),
				"table" => (is_array($field) ? $field[0] : $this->table),
				"alias" => $alias,
                ]
            );
		}
		
		return $this;
	}
	
	public function join($type, $left_field = NULL, $right_field = NULL, $operator = self::OPERATOR_EQ) {
		if($type === NULL) {
			$this->fragments["JOIN"] = array();
		}
		else {
			if(empty($right_field)) {
				// Magic
				$foreign_table = $left_field;
				$right_field = array($foreign_table, "id"); 
				$left_field = "{$foreign_table}_id";
			}
			else {
				if(is_array($left_field) && !is_array($right_field)) {
					$foreign_table = $left_field[0];
				}
				elseif(!is_array($left_field) && is_array($right_field)) {
					$foreign_table = $right_field[0];
				}
			}
			
			\array_push($this->fragments["JOIN"], [
				"join-type" => $type,
				"left-field" => (is_array($left_field) ? $left_field[1] : $left_field),
				"left-table" => (is_array($left_field) ? $this->model->addPrefix($left_field[0]) : $this->table),
				"right-field" => (is_array($right_field) ? $right_field[1] : $right_field),
				"right-table" => (is_array($right_field) ? $this->model->addPrefix($right_field[0]) : $this->table),
				"operator" => $operator,
				"foreign-table" => $this->model->addPrefix($foreign_table),
				"ON" => [],
			]);
		}
		
		return $this;
	}
	
	public function innerJoin($left_field, $right_field = NULL, $operator = self::OPERATOR_EQ) {
		$this->join(self::JOIN_INNER, $left_field, $right_field, $operator);
		return $this;
	}
	
	public function leftJoin($left_field, $right_field = NULL, $operator = self::OPERATOR_EQ) {
		$this->join(self::JOIN_LEFT, $left_field, $right_field, $operator);
		return $this;
	}
    
    public function on($field = NULL, $value = "", $operator = self::OPERATOR_EQ) {
        $lastjoin = count($this->fragments["JOIN"]) - 1;
        if($field === NULL) {
            $this->fragments["JOIN"][$lastjoin]["ON"] = [];
        }
        else {
            \array_push($this->fragments["JOIN"][$lastjoin]["ON"], [
                "field" => $field,
				"value" => $value, 
				"operator" => $operator,
            ]);
        }
        
        return $this;
    }
	
	public function where($field = NULL, $value = "", $operator = self::OPERATOR_EQ) {
		if($field === NULL) {
			$this->fragments["WHERE"] = [];
		}
		else {
			\array_push($this->fragments["WHERE"], [
                "field" => (is_array($field) ? $field[1] : $field),
				"table" => (is_array($field) ? $this->model->addPrefix($field[0]) : $this->table),
				"value" => $value, 
				"operator" => $operator,
            ]);
		}
		
		return $this;
	}	
	
	public function orderBy($field = NULL, $order = parent::ORDER_ASC, $inversion = false) {
		if($field === NULL) {
			$this->fragments["ORDERBY"] = [];
		}
		else {
			\array_push($this->fragments["ORDERBY"], ["field" => (is_array($field) ? $field[1] : $field),
				"table" => (is_array($field) ? $this->model->addPrefix($field[0]) : $this->table),
				"order" => $order, 
				"inversion" => $inversion
                ]);
		}
		return $this;
	}
	
	public function orderByCondition($condition_field, $value, $operator, $true_field, $false_field) {	
		\array_push($this->fragments["ORDERBY"], [
			"conditional" => true, 
			"condition-field" => (is_array($condition_field) ? $condition_field[1] : $condition_field),
			"condition-table" => (is_array($condition_field) ? $condition_field[0] : $this->table),
			"condition-value" => $value, 
			"operator" => $operator, 
			"true-field" => $true_field, 
			"false-field" => $false_field
		]);
		
		return $this;
	}
	
	public function fetch() {
		$this->executeIfNeeded();
		
		if($this->result[0] == false) {
			throw new \Exception(vsprintf("DatabaseError [%s,%s]: %s", $this->result[1]->errorInfo()));
			return false;
		}
		else {
			return $this->result[1]->fetch(\PDO::FETCH_ASSOC);
		}
	}
	
	public function fetchObject($classname, array $arguments = []) {
		$this->executeIfNeeded();
		
		if($this->result[0] == false) {
			throw new \Exception(vsprintf("DatabaseError [%s,%s]: %s", $this->result[1]->errorInfo()));
		}
		else {
			return $this->result[1]->fetchObject($classname, $arguments);
		}
	}
	
	public function execute() {
		$ret = $this->buildQuery();
		if(LOGD_SHOW_DEBUG_SQL) { debug("<b>Query:</b>\n&lt;".$ret[0]."&gt;\n"); }
		
		$prepared = $this->model->getDbh()->prepare($ret[0]);
		$result = $prepared->execute($ret[1]);
		
		return [$result, $prepared];
	}
	
	protected function buildQuery() {
		$query = "";
		$this->args = array();
		
		$query .= $this->buildSelectFragment();
        $query .= $this->buildFromFragment();
        $query .= $this->buildJoinFragment();
		$query .= $this->buildWhereFragment();
		$query .= $this->buildOrderByFragment();
		
		return array($query, $this->args);
	}
    
    private function buildSelectFragment() {
        $query = "";
        $query .= "SELECT\n\t";
		if(empty($this->fragments["SELECT"])) {
			if(empty($this->fragments["JOINS"])) {
				$query .= "*";
			}
			else {
				$query .= sprintf("`%s`.*", $this->table);
			}
		}
		else {
			$i = 0;
			foreach($this->fragments["SELECT"] as $fieldInfo) {
				if($i > 0) {
					$query .= ",\n\t";
				}
				
				if($fieldInfo["field"] instanceof SQLFunction) {
					$function = $fieldInfo["field"]->get_functionname();
					$fieldclass = $fieldInfo["field"]->get_field();
					
					if(is_array($fieldclass)) {
						$table = $fieldclass[0];
						$field = $fieldclass[1];
					}
					else {
						$table = $this->model->addPrefix($fieldInfo["table"]);
						$field = $fieldclass;
					}
					
					if(!empty($fieldInfo["alias"])) {
						$alias = " AS \"".$fieldInfo["alias"]."\"";
					}
					else {
						$alias = "";
					}
					
					if($field == "*") {
						$query .= sprintf("%s(*)%s", $function, $alias);
					}
					else {
						$query .= sprintf("%s(`%s`.`%s`)%s", $function, $table, $field, $alias);
					}
				}
				else {
					if(empty($fieldInfo["alias"])) {
						if($fieldInfo["field"] == "*") {
							$query .= sprintf("`%s`.*", $this->model->addPrefix($fieldInfo["table"]));
						}
						else {
							$query .= sprintf("`%s`.`%s`", $this->model->addPrefix($fieldInfo["table"]), $fieldInfo["field"]);
						}
					}
					else {
						$query .= sprintf("`%s`.`%s` AS \"%s\"", $this->model->addPrefix($fieldInfo["table"]), $fieldInfo["field"], $fieldInfo["alias"]);
					}
				}
				
				$i++;
			}
		}
        return $query;
    }
	
    private function buildFromFragment() {
        $query = "";
		$query .= sprintf("\nFROM `%s`", $this->table);
        return $query;
    }
    
    private function buildJoinFragment() {
        $query = "";
        if(!empty($this->fragments["JOIN"])) {
			foreach($this->fragments["JOIN"] as $clause) {
				$query .= sprintf("\n%s JOIN `%s` ON\n\t", $clause["join-type"], $clause["right-table"]);
				// In principle, there could be more than one on-condition. I will support those if I need them. Later.
				$query .= sprintf(
					"`%s`.`%s` %s `%s`.`%s`",
					$clause["left-table"], $clause["left-field"],
					$clause["operator"],
					$clause["right-table"], $clause["right-field"]
				);
                
                // Additional ON-Clauses
                foreach($clause["ON"] as $onclause) {
                    $query .= " AND\n\t";
					
					if(is_array($onclause["field"])) {
						$table = $onclause["field"][0];
						$field = $onclause["field"][1];
					}
					else {
						$table = $clause["foreign-table"];
						$field = $onclause["field"];
					}
                    
                    if(is_null($onclause["value"])) {
                        if($onclause["operator"] == self::OPERATOR_EQ) {
                            $onclause["operator"] = self::OPERATOR_IS;
                        }
                        elseif($onclause["operator"] == self::OPERATOR_NEQ) {
                            $onclause["operator"] = self::OPERATOR_ISNOT;
                        }

                        $query .= sprintf("`%s`.`%s` %s NULL", $table, $field, $onclause["operator"]);
                    }
                    else {
                        $fieldvar = ":".$table."_on_field_".$field;
                        $query .= sprintf("`%s`.`%s` %s %s", $table, $field, $onclause["operator"], $fieldvar);
                        $this->args[$fieldvar] = $onclause["value"];
                    }
                }
			}
		}
        return $query;
    }
    
    private function buildWhereFragment() {
        $query = "";
        if(!empty($this->fragments["WHERE"])) {
			$query .= "\nWHERE\n\t";
			
			$i = 0;
			foreach($this->fragments["WHERE"] as $clause) {
				if($i > 0) {
					$query .= " AND\n\t";
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
					$this->args[$fieldvar] = $clause["value"];
				}
				
				$i++;
			}
		}
        return $query;
    }
    
    private function buildOrderByFragment() {
        $query = "";
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
						$this->args[$fieldvar] = $clause["condition-value"];
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
        return $query;
    }
    
	public function setCountableColumn($col) {
		$this->countable_column = $col;
		return $this;
	}
	
	public function count() {
		$clone = clone($this);
		$clone->select(NULL)->select(new SQLFunction("COUNT", $this->countable_column), "c");
		$row = $clone->fetch();
		return (int)$row["c"];
	}
}
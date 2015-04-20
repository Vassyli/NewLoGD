<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Query;

// UPDATE accounts SET var=:var, id=:id WHERE key=:key

/**
 * Builds an UPDATE database query and executes it.
 */
class Update extends Base {
    use Writable;
    
    protected $model = NULL;
	protected $table = "";
    
    protected $fragments = ["FIELDS" => [], "VALUES" => [0 => []], "WHERE" => []];
	
	protected $is_executed = false;
	protected $result = NULL;
	protected $affected_rows = [];
    protected $insertid = 0;
    
    protected $args = [];
    
    public function addPair($field, $value) {
        $this->addField($field);
        $this->addValue($value, 0);
        return $this;
    }
    
    public function where($field = NULL, $value = "", $operator = self::OPERATOR_EQ) {
        if($field === NULL) {
			$this->fragments["WHERE"] = [];
		}
		else {
            if(is_array($field) or is_array($value)) {
                throw new \Exception("Query\Update->where: You must not use array as arguments.");
            }
			\array_push($this->fragments["WHERE"], [
                "field" => $field,
				"table" => $this->table,
				"value" => $value, 
				"operator" => $operator,
            ]);
		}
        
        return $this;
    }
    
    public function execute() {
		if($this->is_executed === false) {
			$ret = $this->buildQuery();
			if(LOGD_SHOW_DEBUG_SQL) { debug("<b>Query:</b>\n&lt;".$ret[0]."&gt;\n"); }
			$prepared = $this->model->get_dbh()->prepare($ret[0]);
			
			$result = $prepared->execute($ret[1]);
			
			return $result == true ? $prepared->rowCount() : false;
		}
	}
    
    protected function buildQuery() {  
        $query = "UPDATE `".$this->table."` SET\n";
        $query.= $this->buildSetFragment();
        $query.= $this->buildWhereFragment();
		
		return array($query, array_merge($this->fragments["VALUES"][0], $this->args));
	}
    
    protected function buildSetFragment() {
        $query = "";
		$i = 0;
        
        foreach($this->fragments["FIELDS"] as $field) {
            if($i > 0) {
                $query .= ",\n\t";
            }
            
            if($field["static"]) {
                $query .= "`"
                    .$field["fieldname"]
                    ."` = "
                    .($field["static-value"] instanceof SQLFunction 
                        ? $field["static-value"]->get_functionname()
                        : "'".$field["static-value"]."'")
                    ."()";
            }
            else {
                $query .= "`".$field["fieldname"]."` = ?";
            }
            $i++;
        }
        
        return $query;
    }
    
    protected function buildWhereFragment() {
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
					
					$query .= "`".$clause["field"]."` ".$clause["operator"]." NULL";
				}
				else {
					$fieldvar = ":".$clause["table"]."_field_".$clause["field"];
					$query .= "`".$clause["field"]."` ".$clause["operator"]." ?";
					array_push($this->args, $clause["value"]);
				}
				
				$i++;
			}
		}
        else {
            debug("One of your update queries does not have any WHERE clauses!");
        }
        return $query;
    }
}

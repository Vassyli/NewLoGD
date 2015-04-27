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
 * Builds a DELETE database query and executes it.
 */
class Delete extends Base {    
    protected $model = NULL;
	protected $table = "";
    
    protected $fragments = ["WHERE" => []];
	
	protected $is_executed = false;
	protected $result = NULL;
	protected $affected_rows = [];
    protected $insertid = 0;
    
    protected $args = [];
    
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
			if(!LOGD_SHOW_DEBUG_SQL) { debug("<b>Query:</b>\n&lt;".$ret[0]."&gt;\n"); }
			$prepared = $this->model->get_dbh()->prepare($ret[0]);
			
			$result = $prepared->execute($ret[1]);
			
			return $result == true ? $prepared->rowCount() : false;
		}
	}
    
    protected function buildQuery() {  
        $query = "DELETE FROM `".$this->table."`\n";
        $query.= $this->buildWhereFragment();
		
		return [$query, $this->args];
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
                    $clause["operator"] = ($clause["operator"] == self::OPERATOR_EQ) ? self::OPERATOR_IS : self::OPERATOR_ISNOT;					
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

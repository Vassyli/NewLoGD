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
 * Builds an INSERT INTO database query and executes it.
 */
class InsertInto extends Base {
    use Writable;
    
	protected $model = NULL;
	protected $table = "";
	
	protected $fragments = ["FIELDS" => [], "VALUES" => []];
	
	protected $is_executed = false;
	protected $result = NULL;
	protected $insertid = array();
    
    public function addFields() {
		$args = func_get_args();
		foreach($args as $field) {
            $this->addField($field);
		}
		
		return $this;
	}
    
    public function addValues() {
		$unstaticfieldcount = $this->fieldcount - $this->staticfieldcount;
        $unusedfields = $unstaticfieldcount - count($this->fragments["VALUES"]);
		
        if($unusedfields == 0) {
			throw new \Exception("QueryError: insertInto->addValues() has to be calles after insertInto->addFields()!");
		}
		elseif($unusedfields <> func_num_args()) {
			throw new \Exception(sprintf("QueryError: insertInto->addValues(): Amount of Values (%s) does not match amount of Fields (%s)", func_num_args(), $unstaticfieldcount));
		}
		else {
			$args = func_get_args();
            array_push($this->fragments["VALUES"], $args);     
		}
		
		return $this;
	}
	
	public function execute() {
		if($this->is_executed === false) {
			$ret = $this->buildQuery();
			if(LOGD_SHOW_DEBUG_SQL) { debug("<b>Query:</b>\n&lt;".$ret[0]."&gt;\n"); }
			
			$prepared = $this->model->getDbh()->prepare($ret[0]);
			
			// Execute for every value-Set
			foreach($ret[1] as $valueset) {
				$result = $prepared->execute($valueset);
				array_push($this->insertid, $this->model->getDbh()->lastInsertId());
			}
			
			return array($this->insertid);
		}
	}
	
	protected function buildQuery() {
		$fields = "";
		$values = "";
		$i = 0;
		
		// Prepare Value-Fields
		foreach($this->fragments["FIELDS"] as $field) {
			if($i > 0) {
				$fields .= ",\n\t";
				$values .= ",\n\t";
			}
			
			$fields .= sprintf("`%s`", $field["fieldname"]);
			
			if($field["static"]) {
				if($field["static-value"] instanceof SQLFunction) {	
					$values .= sprintf("%s()", $field["static-value"]->get_functionname());
				}
				else {
					$values .= sprintf("'%s'", $field["static-value"]);
				}
			}
			else {
				$values .= "?";
			}
			
			$i++;
		}
		
		$query = sprintf("INSERT INTO `%s` (\n\t%s\n) VALUES (\n\t%s\n)", $this->table, $fields, $values);
		
		return array($query, $this->fragments["VALUES"]);
	}
}
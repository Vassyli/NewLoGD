<?php

namespace Query;

class InsertInto extends Base {
	protected $model = NULL;
	protected $table = "";
	
	protected $fragments = array("FIELDS" => array(), "VALUES" => array());
	protected $fieldcount = 0;
	protected $staticfieldcount = 0;
	
	protected $is_executed = false;
	protected $result = NULL;
	protected $insertid = array();
	
	public function __construct(\Model $model, $table) {
		$this->model = $model;
		$this->table = $table;
	}
	
	public function addFields() {
		$args = func_get_args();
		foreach($args as $arg) {
			$this->fieldcount++;
			
			if(is_array($arg)) {
				array_push($this->fragments["FIELDS"], array(
					"fieldname" => $arg[0],
					"static" => true,
					"static-value" => $arg[1]
				));
				
				$this->staticfieldcount++;
			}
			else {
				array_push($this->fragments["FIELDS"], array(
					"fieldname" => $arg,
					"static" => false,
					"static-value" => NULL
				));
			}
		}
		
		return $this;
	}
	
	public function addValues() {
		$unstaticfieldcount = $this->fieldcount - $this->staticfieldcount;
		if($unstaticfieldcount == 0) {
			throw new \Exception("QueryError: insertInto->addValues() has to be calles after insertInto->addFields()!");
		}
		elseif($unstaticfieldcount <> func_num_args()) {
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
			$ret = $this->build_query();
			if(LOGD_SHOW_DEBUG_SQL) { debug("<b>Query:</b>\n&lt;".$ret[0]."&gt;\n"); }
			
			$prepared = $this->model->get_dbh()->prepare($ret[0]);
			
			// Execute for every value-Set
			foreach($ret[1] as $valueset) {
				$result = $prepared->execute($valueset);
				array_push($this->insertid, $this->model->get_dbh()->lastInsertId());
			}
			
			return array($this->insertid);
		}
	}
	
	protected function build_query() {
		$table = $this->model->add_prefix($this->table);
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
					$values .= sprintf("\"%s\"", $field["static-value"]);
				}
			}
			else {
				$values .= "?";
			}
			
			$i++;
		}
		
		$query = sprintf("INSERT INTO `%s` (\n\t%s\n) VALUES (\n\t%s\n)", $table, $fields, $values);
		
		return array($query, $this->fragments["VALUES"]);
	}
}
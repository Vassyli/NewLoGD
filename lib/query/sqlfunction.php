<?php

namespace query;

class SQLFunction {
	protected $functionname = "";
	protected $field = "";
	
	public function __construct($functionname, $field = NULL) {
		$this->functionname = $functionname;
		$this->field = $field;
	}
	
	public function get_functionname() {
		return $this->functionname;
	}
	
	public function get_field() {
		return $this->field;
	}
}

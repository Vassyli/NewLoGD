<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Query;

abstract class Base {
	const OPERATOR_EQ = "=";
	const OPERATOR_NEQ = "<>";
	const OPERATOR_GT = ">";
	const OPERATOR_LT = "<";
	const OPERATOR_IS = "IS";
	const OPERATOR_ISNOT = "IS NOT";
	
	const JOIN_INNER = "INNER";
	const JOIN_OUTER = "OUTER";
	
	const ORDER_ASC = "ASC";
	const ORDER_DESC = "DESC";
    
    protected $model = NULL;
	protected $table = "";
    
    protected $is_executed = false;
    
    public function __construct(\Model $model, $table) {
		$this->model = $model;
		$this->table = $this->model->addPrefix($table);;
	}
    
    protected function executeIfNeeded() {
		if($this->is_executed === false) {
			$this->is_executed = true;
			$this->result = $this->execute();
		}
	}
    
    abstract public function execute();
}
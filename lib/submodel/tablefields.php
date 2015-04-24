<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Submodel;

class TableFields implements SubmodelInterface {
	use \lazy;
	
	private $model;
	
	public function __construct(\Model $model) {
		$this->model = $model;
		$this->set_lazy_keys();
		$this->set_lazyset_keys(array("name"));
	}
	
	public function getByTablename($tablename) {
		if($this->has_lazyset("name")) {
			$navs = $this->get_lazyset("name");
			return array();
		}
		else {
			$result = $this->model->from("table_fields")
                ->select("*")
                ->select(array("tables", "name"))
                ->select(array("tables", "options"))
                ->innerjoin("tables")
                ->on("name", $tablename);

			$instances = array();
		
			while($row = $result->fetchObject("\Submodel\Item\TableFieldItem", array($this->model))) {
            //while($row = $result->fetch()) {
				$i = $this->set_lazyset("name", $row);
				array_push($instances, $row);
			}
			
			return $instances;
		}
	}
}
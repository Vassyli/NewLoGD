<?php

namespace Submodel;

class TableFields implements Submodel {
	use \lazy;
	
	private $model;
	
	public function __construct(Model $model) {
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
			//$result = $this->model->from("navigation")->where("page_id", $page_id)->orderby("parentid")->orderby("action", \Query\Select::ORDER_ASC, true);
			$result = $this->model->from("table_fields")
                ->select("*")
                ->select(array("tables", "name"))
                ->innerjoin("tables")
                ->on("name", $tablename);

			$instances = array();
		
			//while($row = $result->fetchObject("\Navigation\Item", array($this->model))) {
            while($row = $result->fetch()) {
				//$i = $this->set_lazyset("name", $row);
				array_push($instances, $row);
			}
			
			return $instances;
		}
	}
}
<?php

class Localmodules implements Submodel {
	use lazy;
	
	private $model;
	
	public function __construct($model) {
		$this->model = $model;
		$this->set_lazy_keys();
		$this->set_lazyset_keys(array("page_id"));
	}
	
	public function getby_page_id($page_id) {
		if($this->has_lazyset("page_id")) {
			$navs = $this->get_lazyset("page_id");
			return array();
		}
		else {
			$result = $this->model->from("localmodule")
				->select("*")
				->select(array("page_localmodule_xref", "config"), "pageconfig")
				->innerjoin("id", array("page_localmodule_xref", "localmodule_id"));
				
			while($row = $result->fetch()) {
				var_dump($row);
			}
			//$result = $this->model->from("navigation")->where("page_id", $page_id)->orderby("parentid")->orderby("action", \Query\Select::ORDER_ASC, true);
			//$result = $this->model->from("navigation")->where("page_id", $page_id)->orderby("parentid")->orderby_condition("action", NULL, \Query\Select::OPERATOR_EQ, "action", "sort")->orderby("sort");
			/*$instances = array();
		
			while($row = $result->fetchObject("\Navigation\Item", array($this->model))) {
				$i = $this->set_lazyset("page_id", $row);
				array_push($instances, $i);
			}
			
			return $instances;*/
		}
	}
}
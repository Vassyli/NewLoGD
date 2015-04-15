<?php

namespace Submodel;

class Localmodules implements SubmodelInterface {
	use \lazy;
	
	private $model;
	
	public function __construct(\Model $model) {
		$this->model = $model;
		$this->set_lazy_keys(array("id", "classname"));
		$this->set_lazyset_keys(array("page_id"));
	}
	
	public function getby_page_id($page_id) {
		if($this->has_lazyset("page_id")) {
			$navs = $this->get_lazyset("page_id");
			return array();
		}
		else {
			$result = $this->model->from("localmodules")
				->select("*")
				->select(array("pages_localmodules_xref", "config"), "pageconfig")
				->innerjoin("id", array("pages_localmodules_xref", "localmodule_id"))
				->where(array("pages_localmodules_xref", "page_id"), $page_id)
				->where("active", 1);
				
			$set = array();
				
			while($row = $result->fetch()) {
				$classname = sprintf("\Localmodule\%s", filter_var($row["class"], FILTER_CALLBACK, array("options" => "filter_nonalpha")));
				$instance = new $classname($this->model, $row, $this->model->get("Pages")->getbyId($page_id));
				$this->set_lazyset("page_id", $instance);
				array_push($set, $instance);
			}
			
			return $set;
		}
	}
}
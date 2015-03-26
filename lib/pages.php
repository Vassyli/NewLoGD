<?php

class Pages implements Submodel {
	use lazy;
	
	private $model;
	
	public function __construct($model) {
		$this->model = $model;
		$this->set_lazy_keys(array("action"));
	}
	
	public function getby_action($action) {
		//if(!isset($this->lazy["action"][$action])) {
		if($this->has_lazy("action", $action) === false) {
			$query = $this->model->from("page")->where("action", $action);
			$row = $query->fetch();
			
			$page = NULL;
			
			if($row === false) {
				// Page not found
			}
			else {
				$classname = sprintf("\Page\%s", filter_var($row["type"], FILTER_CALLBACK, array("options" => "filter_nonalpha")));
				
				try {
					if(class_exists($classname)) {
						$page = new $classname($this->model, $row);
						$this->set_lazy($page);
					}
				}
				catch(LogicException $e) {
					// Lalelu..
				}
			}
			
			return $page;
		}
		else {
			return $this->get_lazy("action", $action);
		}
	}
}
<?php

class Pages implements Submodel {
	use lazy;
	
	private $model;
	
	public function __construct($model) {
		$this->model = $model;
		$this->set_lazy_keys(array("id", "action"));
	}
	
	public function getby_id($id) {
		if($this->has_lazy("id", $id) === false) {
			$query = $this->model->from("pages")
				->where("id", $id);
				
			$row = $query->fetch();
			
			$page = NULL;
			
			if($row === false) {
				throw new Exception(sprintf("Page(id=%i) was not found in database.", $id));
			}
			else {
				$classname = $this->get_classname($row["type"]);
				
				if(class_exists($classname)) {
					$page = new $classname($this->model, $row);
					$this->set_lazy($page);
				}
			}
		}
		else {
			return $this->get_lazy("id", $id);
		}
	}
	
	public function getby_action($action) {
		//if(!isset($this->lazy["action"][$action])) {
		if($this->has_lazy("action", $action) === false) {
			$query = $this->model->from("pages")
				->where("action", $action);
			$row = $query->fetch();
			
			$page = NULL;
			
			if($row === false) {
				// Page not found
				$page = new \Page\Error404($this->model, $action);
				$this->set_lazy($page);
			}
			else {
				$classname = $this->get_classname($row["type"]);
				
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
	
	protected function get_classname($type) {
		return sprintf("\Page\%s", filter_var($type, FILTER_CALLBACK, array("options" => "filter_nonalpha")));
	}
}
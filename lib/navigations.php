<?php

class Navigations implements Submodel {
	use lazy;
	
	private $model;
	
	public function __construct($model) {
		$this->model = $model;
		$this->set_lazy_keys();
		$this->set_lazyset_keys(array("page_id"));
	}
	
	public function getby_pageid($pageid) {
		if($this->has_lazyset("page_id")) {
			$navs = $this->get_lazyset("page_id");
			return array();
		}
		else {
			$result = $this->model->from("navigation")->where("page_id", $pageid)->orderby("parentid");
			$instances = array();
		
			while($row = $result->fetchObject("\Navigation\Item", array($this->model))) {
				$i = $this->set_lazyset("page_id", $row);
				array_push($instances, $i);
			}
			
			return $instances;
		}
	}
}
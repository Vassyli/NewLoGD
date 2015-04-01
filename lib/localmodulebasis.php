<?php

abstract class LocalmoduleBasis implements \LocalmoduleAPI, \Basicmodelitem {
	protected $model;
	protected $page;
	
	protected $id;
	protected $class;
	
	public function __construct($model, $row, $page = NULL) {
		$this->model = $model;
		$this->page = $page;
		
		$this->id = (int)$row["id"];
		$this->class = $row["class"];
		$this->name = $row['name'];
		$this->description = $row['description'];
		$this->active = (bool)$row['active'];
		
		$this->set_pageconfig($row['pageconfig']);
	}
	
	public function get_id() {return $this->id;}
	public function get_class() {return $this->class; }
	public function get_name() {return $this->name;}
	public function get_description() {return $this->description;}
	
	protected function set_pageconfig($pageconfig) {
		$this->pageconfig = json_decode($pageconfig, true);
	}
	
	public function get_pageconfig_field($key) {
		if(isset($this->pageconfig[$key])) {
			return $this->pageconfig[$key];
		}
	}
}
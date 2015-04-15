<?php

abstract class LocalmoduleBasis implements \LocalmoduleAPI, \Basicmodelitem {
	protected $model;
	protected $page;
	
	protected $id;
	protected $class;
	
	public function __construct(\Model $model, array $row, $page = NULL) {
		$this->model = $model;
		$this->page = $page;
		
		$this->id = (int)$row["id"];
		$this->class = $row["class"];
		$this->name = $row['name'];
		$this->description = $row['description'];
		$this->active = (bool)$row['active'];
		
		$this->setPageconfig($row['pageconfig']);
	}
	
	public function getId() {return $this->id;}
	public function getClass() {return $this->class; }
	public function getName() {return $this->name;}
	public function getDescription() {return $this->description;}
	
	protected function setPageconfig($pageconfig) {
		$this->pageconfig = json_decode($pageconfig, true);
	}
	
	public function getPageconfigField($key) {
		if(isset($this->pageconfig[$key])) {
			return $this->pageconfig[$key];
		}
	}
    
    protected function getModuleGameUri() {
        return get_gameuri($this->page->getAction(), array_merge(array($this->getClass()), func_get_args()));
    }
}
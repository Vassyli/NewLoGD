<?php

namespace Navigation;

class Item implements \Truemodelitem {
	private $model;
	
	private $id = 0;
	private $parentid = NULL;
	private $page_id = 0;
	private $action = NULL;
	private $title = "";
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function get_id() {
		return $this->id;
	}
}
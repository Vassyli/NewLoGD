<?php

namespace Navigation;

class Item implements \Truemodelitem, ItemAPI {
	private $model;
	
	const FIELD_ID = "id";
	const FIELD_PARENTID = "parentid";
	const FIELD_PAGE_ID = "page_id";
	const FIELD_ACTION = "action";
	const FIELD_TITLE = "";
	
	const DEFAULT_ID = 0;
	const DEFAULT_PARENTID = NULL;
	const DEFAULT_PAGE_ID = 0;
	const DEFAULT_ACTION = NULL;
	const DEFAULT_TITLE = "";
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function __set($name, $value) {
		switch($name) {
			// Integers
			case self::FIELD_ID:
			case self::FIELD_PARENTID:
			case self::FIELD_PAGE_ID:
				if($value === NULL) {
					$this->$name = NULL;
				}
				else {
					$this->$name = (int)$value;
				}
				break;
			
			// String Rest
			default:
				$this->$name = $value;
				break;
		}
	}
	
	public function get_id()       {return $this->id;}
	public function get_parentid() {return $this->parentid;}
	public function get_action()   {return $this->action;}
	public function get_title()    {return $this->title;}
}
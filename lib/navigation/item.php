<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

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
	
	public function __construct(\Model $model) {
		$this->model = $model;
	}
	
	public function __set($name, $value) {
		switch($name) {
			// Integers
			case self::FIELD_ID:
			case self::FIELD_PARENTID:
			case self::FIELD_PAGE_ID:
				$this->$name = ($value === NULL) ? NULL : intval($value);
				break;
			
			// String Rest
			default:
				$this->$name = $value;
				break;
		}
	}
	
	public function getId()       {return $this->id;}
	public function getParentid() {return $this->parentid;}
	public function getAction()   {return $this->action;}
    public function getParsedAction() { return get_gameuri($this->getAction()); }
	public function getTitle()    {return $this->title;}
}
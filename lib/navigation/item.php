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
	const FIELD_TITLE = "title";
    const FIELD_SORT = "sort";
    const FIELD_FLAGS = "flags";
	
	const DEFAULT_ID = 0;
	const DEFAULT_PARENTID = NULL;
	const DEFAULT_PAGE_ID = 0;
	const DEFAULT_ACTION = NULL;
	const DEFAULT_TITLE = "";
    const DEFAULT_SORT = 0;
    const DEFAULT_FLAGS = 3;
    
    const FLAG_IS_EDITABLE = 1;
    const FLAG_IS_DELETABLE = 2;
    
    protected $has_changed = false;
	
	public function __construct(\Model $model) {
		$this->model = $model;
	}
	
	public function __set($name, $value) {
		switch($name) {
			// Integers
			case self::FIELD_ID:
			case self::FIELD_PARENTID:
			case self::FIELD_PAGE_ID:
            case self::FIELD_FLAGS:
            case self::FIELD_SORT:
				$this->$name = ($value === NULL) ? NULL : intval($value);
				break;
			
			// String Rest
			default:
				$this->$name = $value;
				break;
		}
	}
	
	public function getId()       {return $this->id;}
    public function getPage_Id() { return $this->page_id; }
	public function getParentid() {return $this->parentid;}
	public function getAction()   {return $this->action;}
    public function getParsedAction() { return get_gameuri($this->getAction()); }
	public function getTitle()    {return $this->title;}
    public function getSort()     { return $this->sort; }
    // @inheritDoc
	public function isEditable() { return ($this->flags & self::FLAG_IS_EDITABLE ? true : false); }
	// @inheritDoc
	public function isDeletable() { return ($this->flags & self::FLAG_IS_DELETABLE ? true : false); }
    public function isParentable() {
        if($this->parentid === null and $this->action === null) {
            return true;
        }
        else return false;
    }
    
    protected function set($key, $value) {
        $this->has_changed = true;
        $this->$key = $value;
    }
    public function setPage_Id($page_id) { $this->set("page_id", $page_id); }
    public function setParentid($parentid) { $this->set("parentid", $parentid); }
    public function setAction($action) { $this->set("action", $action); }
    public function setTitle($title) { $this->set("title", $title); }
    public function setSort($sort) { $this->set("sort", $sort); }
    
    public function save() {
        if($this->has_changed) {
            $return = $this->model->get("Navigations")->save($this);
            return $return;
        }
        else {
            return 0;
        }
    }
}
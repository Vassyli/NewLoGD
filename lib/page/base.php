<?php

namespace page;

use \Navigation;

abstract class Base implements api, \Basicmodelitem {
	/** @var Model Contains a reference to the Model class */
	protected $model;
	
	/** @var int db-col id */
	protected $id = 0;
	/** @var string db-col type, defines the class used for page display */
	protected $type = "";
	/** @var string db-col action, to which action this page entry belongs */
	protected $action = "";
	/** @var string db-col title, the title of the page */
	protected $title = "";
	/** @var string db-col subtitle, the subtitle of the page */
	protected $subtitle = "";
	/** @var string db-col content, the text-content of the page */
	protected $content = "";
	/** @var int flags, the flag */
	protected $flags = 0;
	
	/** @var array contains additional arguments passed on to this page */
	protected $arguments = array();

	/**
	 * The constructor.
	 * 
	 * Maps the array $row
	 */
	public function __construct(\Model $model, array $row) {
		$this->model = $model;
		
		$this->id = intval($row['id']);
		$this->type = $row['type'];
		$this->action = $row['action'];
		$this->title = $row['title'];
		$this->subtitle = $row['subtitle'];
		$this->content = $row['content'];
		$this->flags = intval($row['flags']);
		$this->access = intval($row['access']);
		
		debug(sprintf(
			"<b>Page Flags:</b>\n  [%s] Editable\n  [%s] Deletable\n  [%s] No parse\n  [%s] Keep HTML",
			($this->is_editable()?"X":" "),
			($this->is_deletable()?"X":" "),
			($this->use_parser()?" ":"X"),
			($this->keep_html()?"X":" ")
		));
	}
	
	// @inheritDoc
	public function set_arguments(array $args) {$this->arguments = $args;}
	
	// @inheritDoc
	public function get_model() { return $this->model; }
	// @inheritDoc
	public function get_id() { return $this->id; }
	// @inheritDoc
	public function get_type() { return $this->type; }
	// @inheritDoc
	public function get_action() { return $this->action; }
	// @inheritDoc
	public function get_title() { return $this->title; }
	// @inheritDoc
	public function get_subtitle() { return $this->subtitle; }
	// @inheritDoc
	public function get_content() {return $this->content;}
	// @inheritDoc
	public function get_flags() { return $this->flags; }
	// @inheritDoc
	public function check_access($flag) {
		return $this->access & $flag ? true : false;
	}
	
	// @inheritDoc
	public function is_editable() { return ($this->flags & self::FLAG_IS_EDITABLE ? true : false); }
	// @inheritDoc
	public function is_deletable() { return ($this->flags & self::FLAG_IS_DELETABLE ? true : false); }
	// @inheritDoc
	public function use_parser() { return ($this->flags & self::FLAG_NO_PARSE ? false : true); }
	// @inheritDoc
	public function keep_html() { return ($this->flags & self::FLAG_KEEP_HTML ? true : false); }
	// @inheritDoc
	public function has_output() {return ($this->flags & self::FLAG_HAS_NO_OUTPUT ? false : true); }
}
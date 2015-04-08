<?php

namespace page;

use \Navigation;

abstract class Base implements api, \Basicmodelitem {
	protected $model;
	
	protected $id = 0;
	protected $type = "";
	protected $action = "";
	protected $title = "";
	protected $subtitle = "";
	protected $content = "";
	protected $flags = 0;
	
	protected $arguments = array();

	public function __construct($model, $row) {
		$this->model = $model;
		
		$this->id = (int)$row['id'];
		$this->type = $row['type'];
		$this->action = $row['action'];
		$this->title = $row['title'];
		$this->subtitle = $row['subtitle'];
		$this->content = $row['content'];
		$this->flags = (int)$row['flags'];
		
		debug(sprintf(
			"<b>Page Flags:</b>\n  [%s] Editable\n  [%s] Deletable\n  [%s] No parse\n  [%s] Keep HTML",
			($this->is_editable()?"X":" "),
			($this->is_deletable()?"X":" "),
			($this->use_parser()?" ":"X"),
			($this->keep_html()?"X":" ")
		));
	}
	
	public function set_arguments($args) {$this->arguments = $args;}
	
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
	public function is_editable() { return ($this->flags & self::FLAG_IS_EDITABLE ? true : false); }
	// @inheritDoc
	public function is_deletable() { return ($this->flags & self::FLAG_IS_DELETABLE ? true : false); }
	// @inheritDoc
	public function use_parser() { return ($this->flags & self::FLAG_NO_PARSE ? false : true); }
	// @inheritDoc
	public function keep_html() { return ($this->flags & self::FLAG_KEEP_HTML ? true : false); }
}
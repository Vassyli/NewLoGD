<?php

namespace page;

use \Navigation;

class Logout extends Base {	
	protected $parser = NULL;
	protected $modules = array();
	
	protected $navigation = NULL;
	
	protected $block_output = false;
	
	protected $login_valid = false;
	
	public function __construct(\Model $model, array $row) {
		parent::__construct($model, $row);
	}
	
	public function initiate() {}
	
	public function execute() {
		// Maybe some additional Account-Management here? Maybe not.
		
		$this->model->get("Session")->logout();
		$this->model->get("Session")->clear();
		header(sprintf("Location: %s", get_gameuri("main")));
	}
	
	public function output() {
	}
	
	public function load_navigation() {}
	public function get_navigation() {}
	protected function load_localmodules() {}
	public function get_parsed_content() {return "";}
	
	public function block_output() {
		$this->block_output = true;
	}
	
	public function unblock_output() {
		$this->block_output = false;
	}
}
<?php

namespace page;

use \Navigation;

class Login extends Base {	
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
		$acc = $this->model->get("Accounts")->getby_email($this->model->get_postvalue("email"));
		if($acc->verify_password($this->model->get_postvalue("password"))) {
			$this->login_valid = 1;
		}
		else {
			$this->login_valid = 0;
		}
	}
	
	public function output() {
		if($this->login_valid > 0) {
			header(sprintf("Location: %s", get_gameuri("ucp")));
		}
		else {
			header(sprintf("Location: %s", get_gameuri("main")));
		}
		
		die("Redirect");
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
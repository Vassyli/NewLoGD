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
		
		if($acc !== false and $acc->verifyPassword($this->model->get_postvalue("password"))) {
			$this->login_valid = 1;
		}
		else {
			$this->login_valid = 0;
		}
		
		if($this->login_valid > 0) {
			$this->model->get("Session")->login();
			$this->model->get("Session")->set_active_account($acc->getId());
			header(sprintf("Location: %s", get_gameuri("ucp")));
		}
		else {
			$this->model->get("Session")->logout();
			$this->model->get("Session")->clear();
			header(sprintf("Location: %s", get_gameuri("main")));
		}
	}
	
	public function output() {
	}
	
	public function loadNavigation() {}
	public function getNavigation() {}
	protected function load_localmodules() {}
	public function get_parsed_content() {return "";}
	
	public function block_output() {
		$this->block_output = true;
	}
	
	public function unblock_output() {
		$this->block_output = false;
	}
}
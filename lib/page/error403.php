<?php

namespace page;

use \Navigation;

class Error403 implements api, errorapi, \Modelitem {
	private $model = NULL;
	
	protected $action = "";
	protected $args = array();
	protected $access = 3;

	public function __construct(\Model $model, array $action) {
		$this->model = $model;
		$this->action = $action[0];
	}
	
	public function set_arguments(array $args) {$this->args = $args;}
	public function get_arguments() { return $this->args; }
	public function execute() {}
	
	public function get_model() { return $this->model; }
	public function get_id() { return -ERROR_ACCESS_FORBIDDEN; }
	public function get_type() { return "error403"; }
	public function get_title() { return "Error 403"; }
	public function get_subtitle() { return "Zugriff verboten."; }
	public function get_action() { return $this->action; }
	public function get_content() { 
		$arr = array(LOGD_URI_ABS, $this->action); 
		array_push($arr, implode("/", $this->args)); 
		
		return sprintf("
				Der Zugriff auf die gewünschte Ressource [%s] ist nicht erlaubt. 
				Sollten Sie mit Absicht auf diese Seite gestossen sein, so schämen 
				Sie sich, dieser Cheatversuch ging schief.. ;)
				
				Sollten Sie versehentlich hier her gelangt sein (zum Beispiel via 
				einem kaputten Navigationspunkt), so gehen Sie einfach eine Seite 
				zurück."
			, implode("/", $arr)
		);
	}
	
	public function get_flags() {return 48;}
	public function check_access($flag) {
		return $this->access & $flag ? true : false;
	}
	public function is_editable() {return false;}
	public function is_deletable(){return false;}
	public function use_parser(){return false;}
	public function keep_html(){return true;}
	public function has_output(){return true;}
	
	public function get_errorcode() { return self::ERROR_ACCESS_FORBIDDEN; }
	
	public function load_navigation() {}
	public function get_navigation() {
		$container = new Navigation\Container();
		return $container;
	}
	
	public function output() {
		return $this->get_content();
	}
	
	public function initiate() {}
}
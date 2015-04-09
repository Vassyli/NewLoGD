<?php

namespace page;

use \Navigation;

class Error404 implements api, errorapi, \Modelitem {
	private $model = NULL;
	
	protected $action = "";
	protected $args = array();

	public function __construct(\Model $model, array $action) {
		$this->model = $model;
		$this->action = $action[0];
	}
	
	public function set_arguments(array $args) {$this->args = $args;}
	public function execute() {}
	
	public function get_model() { return $this->model; }
	public function get_id() { return -404; }
	public function get_type() { return "error404"; }
	public function get_title() { return "Error 404"; }
	public function get_subtitle() { return "Seite nicht gefunden."; }
	public function get_action() { return $this->action; }
	public function get_content() { 
		$arr = array(LOGD_URI_ABS, $this->action); 
		array_push($arr, implode("/", $this->args)); 
		
		return sprintf("
				Die gewünschte Seite unter der Ressource [%s] wurde nicht gefunden. 
				Entweder wurde sie entfernt, jemand hat sich verschrieben, oder ein 
				Krake hat sie entführt. Wir bitten Sie für die Umstände um Verzeihung."
			, implode("/", $arr)
		);
	}
	
	public function get_flags() {return 48;}
	public function is_editable() {return false;}
	public function is_deletable(){return false;}
	public function use_parser(){return false;}
	public function keep_html(){return true;}
	public function has_output(){return true;}
	
	public function get_errorcode() { return self::ERROR_NOT_FOUND; }
	
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
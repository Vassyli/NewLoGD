<?php

namespace page;

use \Navigation;

class Error404 implements api, errorapi, \Modelitem {
	private $model = NULL;
	
	protected $action = "";
	protected $args = array();
	protected $access = 3;

	public function __construct(\Model $model, array $action) {
		$this->model = $model;
		$this->action = $action[0];
	}
	
	public function set_arguments(array $args) {$this->args = $args;}
	public function getArguments() { return $this->args; }
	public function execute() {}
	
	public function getModel() { return $this->model; }
	public function getId() { return -self::ERROR_NOT_FOUND; }
	public function getType() { return "error404"; }
	public function getTitle() { return "Error 404"; }
	public function getSubtitle() { return "Seite nicht gefunden."; }
	public function getAction() { return $this->action; }
	public function getContent() { 
		$arr = array(LOGD_URI_ABS, $this->action); 
		array_push($arr, implode("/", $this->args)); 
		
		return sprintf("
				Die gewünschte Seite unter der Ressource [%s] wurde nicht gefunden. 
				Entweder wurde sie entfernt, jemand hat sich verschrieben, oder ein 
				Krake hat sie entführt. Wir bitten Sie für die Umstände um Verzeihung."
			, implode("/", $arr)
		);
	}
	
	public function getFlags() {return 48;}
	public function checkAccess($flag) {
		return $this->access & $flag ? true : false;
	}
	public function isEditable() {return false;}
	public function isDeletable(){return false;}
	public function useParser(){return false;}
	public function keepHtml(){return true;}
	public function hasOutput(){return true;}
	
	public function get_errorcode() { return self::ERROR_NOT_FOUND; }
	
	public function loadNavigation() {}
	public function getNavigation() {
		$container = new Navigation\Container();
		return $container;
	}
	
	public function output() {
		return $this->getContent();
	}
	
	public function initiate() {}
}
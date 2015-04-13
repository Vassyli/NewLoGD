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
	public function getArguments() { return $this->args; }
	public function execute() {}
	
	public function getModel() { return $this->model; }
	public function getId() { return -self::ERROR_ACCESS_FORBIDDEN; }
	public function getType() { return "error403"; }
	public function getTitle() { return "Error 403"; }
	public function getSubtitle() { return "Zugriff verboten."; }
	public function getAction() { return $this->action; }
	public function getContent() { 
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
	
	public function getFlags() {return 48;}
	public function checkAccess($flag) {
		return $this->access & $flag ? true : false;
	}
	public function isEditable() {return false;}
	public function isDeletable(){return false;}
	public function useParser(){return false;}
	public function keepHtml(){return true;}
	public function hasOutput(){return true;}
	
	public function get_errorcode() { return self::ERROR_ACCESS_FORBIDDEN; }
	
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
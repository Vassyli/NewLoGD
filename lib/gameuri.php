<?php

class GameURI implements JsonSerializable {
	protected $action = "";
	protected $module = "";
	protected $arguments = [];
	
	public function __construct($action, array $arguments = []) {
		$this->action = $action;
		$this->arguments = $arguments;
	}
	
	public function jsonSerialize() {
		return $this->getParsedURI();
	}
	
	public function addArgument() {
		$args = func_get_args();
		foreach($args as $arg) {
			if(is_array($arg)) {
				foreach($arg as $subarg) {
					array_push($this->arguments, $subarg);
				}
			}
			else{
				array_push($this->arguments, $arg);
			}
		}
		return $this;
	}
	
	public function setModule($module) {
		$this->module = $module;
		return $this;
	}
	
	public function getParsedURI() {
		if(LOGD_CONTENTTYPE == "html") {
			$uri = LOGD_URI_ABS . "/" . $this->action;
		}
		else {
			$uri = LOGD_URI_ABS . "/json/" . $this->action;
		}
		
		if(!empty($this->module)) {
			$uri = $uri . "/" . $this->module;
		}

		if(count($this->arguments) > 0) {
			$args = implode("/", $this->arguments);
			return $uri . "/" . $args;
		}
		return $uri;
	}
}

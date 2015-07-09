<?php

class GameURI {
	protected $action = "";
	protected $arguments = [];
	
	public function __construct($action, array $arguments = []) {
		$this->action = $action;
		$this->arguments = $arguments;
	}
	
	public function addArgument() {
		$args = func_get_args();
		foreach($args as $arg) {
			array_push($this->arguments[$arg]);
		}
		return $this;
	}
	
	public function getParsedURI() {
		$uri = LOGD_URI_ABS . "/" . $this->action;

		if(count($this->arguments) > 0) {
			$args = implode("/", $this->arguments);
			return $uri . $args;
		}
		return $uri;
	}
}

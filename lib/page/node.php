<?php

namespace page;

use \Navigation;

class Node extends Base {
	protected $parser = NULL;
	protected $modules = array();
	
	protected $navigation = NULL;
	
	protected $block_output = false;
	
	public function __construct(\Model $model, array $row) {
		parent::__construct($model, $row);
	}
	
	public function initiate() {	
		$this->parser = new \Parser();
		$this->load_localmodules();
	}
	
	public function execute() {
		// Execute Modules
		foreach($this->modules as $module) {
			if(empty($arguments[0]) || $arguments[0] == $module->getClass()) {
				$module->execute();
			}
		}
	}
	
	public function output() {
		$arguments = $this->getArguments();
		$maincontent = $this->get_parsed_content();
		
		$modulecontent = "";
		
		foreach($this->modules as $module) {
			// Execute module only if first argument is empty or equals the module
			if(empty($arguments[0]) || $arguments[0] == $module->getClass()) {
				$modulecontent .= sprintf("\n\n<!--Content(Localmodule\\%s)-->\n%s", $module->getName(), $module->output());
			}
		}
		
		return $maincontent.$modulecontent;
	}
	
	public function loadNavigation() {
		if($this->navigation === NULL) {
			$this->navigation = new Navigation\Container();
			$this->navigation->add_bulk($this->model->get("Navigations")->getby_page_id($this->getId()));
		}
	}
	
	public function getNavigation() {
		return $this->navigation;
	}
	
	protected function load_localmodules() {
		$this->modules = $this->model->get("Localmodules")->getby_page_id($this->getId());
	}
	
	public function get_parsed_content() {
		if($this->block_output === false) {
			$content = $this->getContent();
			
			if($this->keepHtml() === false) {
				$content = HTMLSpecialchars($content, ENT_HTML5, LOGD_ENCODING);
			}
			
			if($this->useParser() === true) {
				$content = $this->parser->parse($content);
			}
			
			return $content;
		}
		else {
			return "<!--Default page output is blocked.-->";
		}
	}
	
	public function block_output() {
		$this->block_output = true;
	}
	
	public function unblock_output() {
		$this->block_output = false;
	}
}
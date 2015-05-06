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
		$this->loadLocalmodules();
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
		$maincontent = $this->getParsedContent();
		
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
			$this->navigation->addBulk($this->model->get("Navigations")->getByPageId($this->getId()));
            
            // Navigational Hook
            foreach($this->modules as $module) {
                // Execute module only if first argument is empty or equals the module
                if(empty($arguments[0]) || $arguments[0] == $module->getClass()) {
                    $module->navigationHook($this->navigation);
                }
            }
		}
	}
	
	public function getNavigation() {
		return $this->navigation;
	}
	
	protected function loadLocalmodules() {
		$this->modules = $this->model->get("Localmodules")->getByPageId($this->getId());
	}
	
	public function getParsedContent() {
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
	
	public function blockOutput() {
		$this->block_output = true;
	}
	
	public function unblockOutput() {
		$this->block_output = false;
	}
}
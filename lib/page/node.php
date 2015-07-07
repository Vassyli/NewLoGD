<?php

namespace page;

use \Navigation;

class Node extends Base implements \hasModules {
	protected $parser = NULL;
	protected $modules = [];
    protected $module_loaded = false;
	
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
        if($this->module_loaded == false) {
            $this->modules = $this->model->get("Localmodules")->getByPageId($this->getId());
            $this->module_loaded = true;
        }
	}
	
	public function getLocalmodule($module) {
		$this->loadLocalmodules();
		
		foreach($this->modules as $mod) {
			if($mod->getClass() == $module) {
				return $mod;
			}
		}
		return NULL;
	}
    
    /**
     * Returns an array of \FormGenerator.
     * @return array array of \FormGenerator
     */
    public function getLocalmodulesForm($action) {
        $this->loadLocalmodules();

        if(empty($this->modules)) {
            return [];
        }
        
        $return = [];
        foreach($this->modules as $module) {
            $config_form = $module->getPageconfigForm($action);
            if($config_form !== NULL) {
                array_push($return, $config_form);
            }
        }
        
        return $return;
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
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
			$module->execute();
		}
	}
	
	public function output() {
		$maincontent = $this->get_parsed_content();
		
		$modulecontent = "";
		
		foreach($this->modules as $module) {
			$modulecontent .= sprintf("\n\n<!--Content(Localmodule\\%s)-->\n%s", $module->get_name(), $this->parser->parse($module->output()));
		}
		
		return $maincontent.$modulecontent;;
	}
	
	public function load_navigation() {
		if($this->navigation === NULL) {
			$this->navigation = new Navigation\Container();
			$this->navigation->add_bulk($this->model->get("Navigations")->getby_page_id($this->get_id()));
		}
	}
	
	public function get_navigation() {
		return $this->navigation;
	}
	
	protected function load_localmodules() {
		$this->modules = $this->model->get("Localmodules")->getby_page_id($this->get_id());
	}
	
	public function get_parsed_content() {
		if($this->block_output === false) {
			$content = $this->get_content();
			
			if($this->keep_html() === false) {
				$content = HTMLSpecialchars($content, ENT_HTML5, LOGD_ENCODING);
			}
			
			if($this->use_parser() === true) {
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
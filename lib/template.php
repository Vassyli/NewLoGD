<?php

class Template {
	protected $tpl_name = "";
	protected $tpl_dir = "";
	
	protected $buffer = "";
	
	protected $parts = array();
	
	protected $parser = NULL;
	
	public function __construct($templatename) {
		$this->tpl_name = $templatename;
		$this->tpl_dir = LOGD_TEMPLATE.$templatename."/";
		$this->parser = new Parser();
	}
	
	public function set_page(\Page\api $page) {
		$this->parts['PAGE'] = $page;
	}
	
	public function set_navigation($navigation) {
		$this->parts['navigation'] = $navigation;
	}
	
	public function set_copyright($copyright) {
		$this->parts['copyright'] = $copyright;
	}
	
	public function set_pagegen($pagegen) {
		$this->parts['pagegen'] = $pagegen;
	}
	
	public function set_debug($debug) {
		$this->parts['debug'] = $debug;
	}
	
	public function get_page() {
		return $this->parts['PAGE'];
	}
	
	public function get_navigation() {
		return $this->parts['navigation'];
	}
	
	public function get_parsed_content() {
		$content = $this->parts['PAGE']->get_content();
		
		if($this->parts['PAGE']->keep_html() === false) {
			$content = HTMLSpecialchars($content, ENT_HTML5, LOGD_ENCODING);
		}
		
		if($this->parts['PAGE']->use_parser() === true) {
			$content = $this->parser->parse($content);
		}
		
		return $content;
	}
	
	public function get_copyright() {
		return $this->parts['copyright'];
	}
	
	public function get_pagegen() {
		return $this->parts['pagegen'];
	}
	
	public function get_debug() {
		return $this->parts['debug'];
	}
	
	public function get_cssfilename() {
		return "default.css";
	}	
	
	protected function get_template_filename() {
		return $this->tpl_dir."template.php";
	}
	
	public function get_template_uribasepath($file = "") {
		return sprintf("%s%s/%s", LOGD_TEMPLATE_URI, $this->tpl_name, $file);
	}
	
	public function get_gameuri($action) {
		return sprintf("%s/%s", LOGD_URI_ABS, $action);
	}
	
	public function output() {
		// Get output buffer content
		$this->set_debug(ob_get_contents());
		ob_clean();
		
		// Get Pagegen
		$this->set_pagegen(sprintf("Time for page generation: %.3f", microtime(true) - LOGD_SCRIPT_START));
		
		// Include the template file
		Include $this->get_template_filename();
		
		$this->buffer = ob_get_clean();
		
		return $this->buffer;
	}
}
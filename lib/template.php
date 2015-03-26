<?php

class Template {
	protected $tpl_name = "";
	protected $tpl_dir = "";
	
	protected $buffer = "";
	
	protected $parts = array();
	
	public function __construct($templatename) {
		$this->tpl_name = $templatename;
		$this->tpl_dir = LOGD_TEMPLATE.$templatename."/";
	}
	
	public function set_page(\Page\api $page) {
		$this->parts['PAGE'] = $page;
	}
	
	public function set_content($content) {
		$this->parts['content'] = $content;
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
	
	public function get_content() {
		return $this->parts['content'];
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
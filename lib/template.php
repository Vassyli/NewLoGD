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
	
	public function setPage(\Page\api $page) {
		$this->parts['PAGE'] = $page;
	}
	
	public function setCopyright($copyright) {
		$this->parts['copyright'] = nl2br($copyright);
	}
	
	public function setPagegen($pagegen) {
		$this->parts['pagegen'] = $pagegen;
	}
	
	public function setDebug($debug) {
		$this->parts['debug'] = $debug;
	}
	
	public function getModel() {
		return $this->parts['PAGE']->getModel();
	}
	
	public function getPage() {
		return $this->parts['PAGE'];
	}
	
	public function getCopyright() {
		return $this->parts['copyright'];
	}
	
	public function getPagegen() {
		return $this->parts['pagegen'];
	}
	
	public function getDebug() {
		return $this->parts['debug'];
	}
	
	public function getCssFilename() {
		return "default.css";
	}	
	
	protected function getTemplateFilename() {
		return $this->tpl_dir."template.php";
	}
	
	public function getTemplateUribasepath($file = "") {
		return sprintf("%s%s/%s", LOGD_TEMPLATE_URI, $this->tpl_name, $file);
	}
	
	public function output() {
		// Get output buffer content
		$this->setDebug(ob_get_contents());
		ob_clean();
		
		// Get Pagegen
		$this->setPagegen(sprintf("Time for page generation: %.3f", microtime(true) - LOGD_SCRIPT_START));
		
		// Include the template file
		Include $this->getTemplateFilename();
		
		$this->buffer = ob_get_clean();
		
		return $this->buffer;
	}
}
<?php

class View {
	private $model = NULL;
	private $controller = NULL;
	
	public function __construct(Controller $controller, Model $model) {
		$this->controller = $controller;
		$this->model = $model;
	}
	
	public function output() {
		// Get template handler
		$template = new Template("default");
		
		// Collect part informations
		$page = $this->model->get("Pages")->getby_action($this->model->get_res_action());
		$navigation = $page->get_navigation();
		
		// Set the template variables
		$template->set_page($page);
		$template->set_navigation($navigation);
		$template->set_copyright(LOGD_COPYRIGHT);
		
		// Get the generated template
		$buffer = $template->output();
		
		// Send a few headers
		if($page instanceof errorapi) {
			http_response_code($page->get_errorcode());
		}
		
		header("Content-type: text/html; charset=utf-8");
		
		print $buffer;
		exit;
	}
}
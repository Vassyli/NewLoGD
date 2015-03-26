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
		//$template->set_title($page->get_title());
		//$template->set_subtitle($page->get_title());
		$template->set_content($page->get_content());
		$template->set_copyright(LOGD_COPYRIGHT);
		
		// Get the generated template
		$buffer = $template->output();
		
		print $buffer;
		exit;
	}
}
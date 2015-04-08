<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */
 /**
  * Collects some data from Model in order to display the rendered page.
  */
class View {
	/** @var Model Contains a reference to the Model class */
	private $model = NULL;
	/** @var Controller Contains a reference to the Controller class */
	private $controller = NULL;
	
	/**
	 * The constructor.
	 *
	 * @param Controller $controller A reference to a instance of the Controller class
	 * @param Model $model A reference to a instance of the Model class
	 */
	public function __construct(Controller $controller, Model $model) {
		$this->controller = $controller;
		$this->model = $model;
	}
	
	/**
	 * Loads the template and runs the processing code needed for rendering the webpage including some HTTP response codes if needed.
	 *
	 * @param void
	 * @return void
	 */
	public function output() {
		// Get template handler
		$template = new Template("default");
		
		// Get Page-Instance
		$page = $this->model->get("Pages")->getby_action($this->model->get_res_action());
		$page->load_navigation();
		
		// Set some additional template variables
		$template->set_page($page);
		$template->set_copyright(LOGD_COPYRIGHT);
		
		// Get the generated template
		$buffer = $template->output();
		
		// Send a few headers if needed
		if($page instanceof errorapi) {
			http_response_code($page->get_errorcode());
		}
		
		// Send content type and charset
		header("Content-type: text/html; charset=utf-8");
		
		// Print rendered content and exit.
		print $buffer;
		exit;
	}
}
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
		// Get Page-Instance
		$page = $this->model->get("Pages")->getbyAction($this->model->getRessourceAction());
		
		if($this->model->contenttype == "json") {
			$buffer = $this->outputJSON($page);
		}
		else {
			$buffer = $this->outputHTML($page);
		}
		
		// Print rendered content and exit.
		print $buffer;
		exit;
	}
	
	protected function outputHTML($page) {
		// Start output handler only if the Page has output
		if($page->hasOutput()) {
			// Get template handler
			$template = new Template("default");

			// Load navigation
			$page->loadNavigation();

			// Set some additional template variables
			$template->setPage($page);
			$template->setCopyright(LOGD_COPYRIGHT);

			// Get the generated template
			$buffer = $template->output();

			// Send a few headers if needed
			if($page instanceof errorapi) {
				http_response_code($page->get_errorcode());
			}

			// Send content type and charset
			header("Content-type: text/html; charset=utf-8");
		}
		else $buffer = "";
		return $buffer;
	}
	
	protected function outputJSON($page) {
		ob_end_clean();
		$loggedin = $this->model->get("Session")->is_loggedin();
		$json = [
			"copyright" => LOGD_COPYRIGHT,
			"loggedin" => $loggedin,
			"account" => $loggedin ? $this->getAccountInfo() : false,
			"login" => $loggedin ? false : $this->getLoginInfo(),
			"logout" => $loggedin ? $this->getLogoutInfo() : false,
		];
		
		// Send a few headers if needed
		if($page instanceof errorapi) {
			http_response_code($page->get_errorcode());
		}
		header("Content-type: application/json; charset=utf-8");
		return json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}
	
	protected function getAccountInfo() {
		return [
			'id' => $this->$model->get("Accounts")->get_active()->getId(),
		];
	}
	
	protected function getLoginInfo() {
		return [
			"form" => [
				"uri" => (new GameURI("login"))->getParsedURI(),
				"method" => "post",
				"fields" => [
					"email" => ["label" => "E-Mail", "type" => "email"],
					"password" => ["label" => "Passwort", "type" => "password"],
				],
			],
		];
	}
}
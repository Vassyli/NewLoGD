<?php

namespace Localmodule;

class Registration extends \LocalmoduleBasis {
	protected $model;
	
	protected $id;
	
	private $form = NULL;
	private $form_state = 0;
	
	public function __construct($model, $row, $page = NULL) {
		parent::__construct($model, $row, $page);
	}
	
	public function execute() {
		debug("Called \\localmodule\\registration->execute();");
		
		$this->form_state = 0;
	}
	
	public function output() {
		debug("Called \\localmodule\\registration->output();");
		
		if($this->form_state >= 0) {
			$this->get_form()
				->add_line(
					$this->get_pageconfig_field("name_fieldname"), 
					"name", 
					NULL, 
					array(
						"min-length" => 0, 
						"max-length" => 3,
						"required" => true,
					))
				->add_password(
					$this->get_pageconfig_field("password1_fieldname"), 
					"password1", 
					NULL, 
					array(
						"min-length" => 0, 
						"max-length" => 3,
						"required" => true,
					))
			;
				
			$text = $this->get_form()->get_HTML();
			
			return $text;
		}
		else {
			return "";
		}
	}
	
	protected function get_form() {
		if($this->form === NULL) {
			$this->form = new \FormGenerator("Registrierung", get_gameuri($this->page->get_action()));
		}
		return $this->form;
	}
}
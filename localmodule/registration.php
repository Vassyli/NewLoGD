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
		$this->form_state = 0;
		
		if($this->model->get_postvalue("register_submit") == 1) {
			// There was something posted - sanitize it!
			try {
				$sanitize = $this->get_form()->sanitize($this->model->get_postarray(), true);
				$this->form_state = -1;
				
				$this->model->get("Accounts")->create($sanitize["name"], $sanitize["password1"], $sanitize["email1"]);
				$this->form_state = -2;
			}
			catch(\Exception $e) {
				$this->form_state = 0;
			}
			
			// Block default page output
			$this->page->block_output();
		}
	}
	
	public function output() {		
		if($this->form_state >= 0) {
			return $this->get_form()->get_HTML();
		}
		elseif($this->form_state == -1) {
			return "Beim Erstellen des Accounts ist etwas schief gegangen.";
		}
		else {
			return "Der Account wurde erfolgreich erstellt.";
		}
	}
	
	protected function get_form() {
		if($this->form === NULL) {
			$this->form = new \FormGenerator("Registrierungs-Formular", get_gameuri($this->page->get_action()));
			$this->form->add_line(
					$this->get_pageconfig_field("name_fieldname"), 
					"name", 
					"", 
					array(
						"min-length" => 1, 
						"max-length" => 50,
						"required" => true,
						"callback" => array(array($this->model->get("Accounts"), "check_name"), "Der Name ist bereits in Verwendung"),
					)
				)
				->add_password(
					$this->get_pageconfig_field("password1_fieldname"), 
					"password1",
					array(
						"min-length" => 8,
						"max-length" => LOGD_PASSWORD_MAXLENGTH,
						"max-bytelength" => LOGD_PASSWORD_MAXLENGTH,
						"required" => true,
						"crosscheck" => "password2",
					)
				)
				->add_password(
					$this->get_pageconfig_field("password2_fieldname"), 
					"password2",
					array(
						"min-length" => 8,
						"max-length" => LOGD_PASSWORD_MAXLENGTH,
						"max-bytelength" => LOGD_PASSWORD_MAXLENGTH,
						"required" => true,
					)
				)
				->add_email(
					$this->get_pageconfig_field("email1_fieldname"),
					"email1",
					"",
					array(
						"max-length" => 100,
						"required" => true,
						"crosscheck" => "email2",
						"callback" => array(array($this->model->get("Accounts"), "check_email"), "Diese Email-Adresse ist bereits in Verwendung"),
					)
				)
				->add_email(
					$this->get_pageconfig_field("email2_fieldname"),
					"email2",
					"",
					array(
						"max-length" => 100,
						"required" => true,
					)
				)
				->add_submitbutton(
					$this->get_pageconfig_field("submitbutton_name"),
					"register_submit",
					"1"
				)
			;
		}
		return $this->form;
	}
}
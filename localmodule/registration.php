<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Localmodule;

class Registration extends \LocalmoduleBasis {
	protected $model;
	
	private $form = NULL;
	private $form_state = 0;
    
    const DEF_REGISTER_FORM = "Ein neues Benutzerkonto erstellen";
    const DEF_REGISTER_NAME = "Name";
    const DEF_REGISTER_PASS1 = "Passwort";
    const DEF_REGISTER_PASS2 = "Passwort (wiederhohlen)";
    const DEF_REGISTER_MAIL1 = "Email";
    const DEF_REGISTER_MAIL2 = "Email (wiederhohlen)";
    const DEF_REGISTER_SUBMIT = "Registrierung abschliessen";
	
	public function __construct(\Model $model, array $row, $page = NULL) {
		parent::__construct($model, $row, $page);
	}
	
	public function execute() {		
		$this->form_state = 0;
		
		if($this->model->getPostvalue("register_submit") == 1) {
			// There was something posted - sanitize it!
			try {
				$sanitize = $this->get_form()->sanitize($this->model->getPostarray(), true);
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
			return $this->get_form()->getHtml();
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
			$this->form = new \FormGenerator($this->getPageconfigField("form-title", self::DEF_REGISTER_FORM), get_gameuri($this->page->getAction()));
			$this->form->addLine(
					$this->getPageconfigField("name_fieldname", self::DEF_REGISTER_NAME), 
					"name", 
					"", 
					array(
						"min-length" => 1, 
						"max-length" => 50,
						"required" => true,
						"callback" => array(array($this->model->get("Accounts"), "check_name"), "Der Name ist bereits in Verwendung"),
					)
				)
				->addPassword(
					$this->getPageconfigField("password1_fieldname", self::DEF_REGISTER_PASS1), 
					"password1",
					array(
						"min-length" => 8,
						"max-length" => LOGD_PASSWORD_MAXLENGTH,
						"max-bytelength" => LOGD_PASSWORD_MAXLENGTH,
						"required" => true,
						"crosscheck" => "password2",
					)
				)
				->addPassword(
					$this->getPageconfigField("password2_fieldname", self::DEF_REGISTER_PASS2), 
					"password2",
					array(
						"min-length" => 8,
						"max-length" => LOGD_PASSWORD_MAXLENGTH,
						"max-bytelength" => LOGD_PASSWORD_MAXLENGTH,
						"required" => true,
					)
				)
				->addEmail(
					$this->getPageconfigField("email1_fieldname", self::DEF_REGISTER_MAIL1),
					"email1",
					"",
					array(
						"max-length" => 100,
						"required" => true,
						"crosscheck" => "email2",
						"callback" => array(array($this->model->get("Accounts"), "check_email"), "Diese Email-Adresse ist bereits in Verwendung"),
					)
				)
				->addEmail(
					$this->getPageconfigField("email2_fieldname", self::DEF_REGISTER_MAIL2),
					"email2",
					"",
					array(
						"max-length" => 100,
						"required" => true,
					)
				)
				->addSubmitButton(
					$this->getPageconfigField("submitbutton_name", self::DEF_REGISTER_SUBMIT),
					"register_submit",
					"1"
				)
			;
		}
		return $this->form;
	}
    
    public function getPageconfigForm($action) {
        $formgenerator = new \FormGenerator($this->getName(), $action);
        $formgenerator->addLine("Formular-Titel", "form-title", $this->getPageconfigField("form-title", self::DEF_REGISTER_FORM))
            ->addLine("Formular-Text für den Namen", "name_fieldname", $this->getPageconfigField("name_fieldname", self::DEF_REGISTER_NAME))
            ->addLine("Formular-Text für das Passwort", "password1_fieldname", $this->getPageconfigField("password1_fieldname", self::DEF_REGISTER_PASS1))
            ->addLine("Formular-Text für die Passwort-Wiederhohlung", "password2_fieldname", $this->getPageconfigField("password2_fieldname", self::DEF_REGISTER_PASS2))
            ->addLine("Formular-Text für die E-Mailadresse", "email1_fieldname", $this->getPageconfigField("email1_fieldname", self::DEF_REGISTER_MAIL1))
            ->addLine("Formular-Text für die E-Mail-Wiederhohlung", "email2_fieldname", $this->getPageconfigField("email2_fieldname", self::DEF_REGISTER_MAIL2))
            ->addLine("Formular-Text für die Bestätigung", "submitbutton_name", $this->getPageconfigField("submitbutton_name", self::DEF_REGISTER_SUBMIT))
            ->addSubmitButton("Submit", "module", $this->getClass());
        return $formgenerator;
    }
}
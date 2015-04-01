<?php

class FormGenerator {
	protected $action;
	protected $formtitle;
	protected $form_elements = array();
	
	const TYPE_LINE_INPUT = 1;
	const TYPE_PASSWORD_INPUT = 2;
	
	public function __construct($formtitle, $action) {
		$this->action = $action;
		$this->formtitle = $formtitle;
	}
	
	public function get_html() {
		$fields = "\n";
		
		foreach($this->form_elements as $inp) {
			$fields.= "\t<div class=\"form-group\">\n";
			$fields.= sprintf("\t\t<label for=\"%s\">%s</label>\n", $inp["id"], $inp["description"]);
			$fields.= "\t\t<div class=\"form-input\">\n";
			switch($inp["type"]) {
				case self::TYPE_PASSWORD_INPUT:
					$fields.= sprintf("\t\t\t<input type=\"password\" id=\"%s\" %s>\n", $inp["id"], empty($inp["validator"]["required"])?:"required");
					break;
				
				case self::TYPE_LINE_INPUT:
				default:
					$fields.= sprintf("\t\t\t<input type=\"text\" id=\"%s\" %s>\n", $inp["id"], empty($inp["validator"]["required"])?:"required");
					break;
			}
			$fields.= "\t\t</div>\n";
			$fields.= "\t</div>\n";
		}
		
		return sprintf("<form action=\"%s\" method=\"post\">\n<fieldset>\n\t<legend>%s</legend>%s</fieldset></form>", $this->action, $this->formtitle, $fields);
	}
	
	protected function add_input($type, $desc, $name, $value, $validator) {
		$this->form_elements[$name] = array(
			"id" => "id_".md5(microtime().$name),
			"type" => $type,
			"description" => $desc,
			"value" => $value,
			"validator" => $validator,
		);
	}
	
	public function add_line($desc, $name, $value, $validator) {
		$this->add_input(self::TYPE_LINE_INPUT, $desc, $name, $value, $validator);
		return $this;
	}
	
	public function add_password($desc, $name, $value, $validator) {
		$this->add_input(self::TYPE_PASSWORD_INPUT, $desc, $name, $value, $validator);
		return $this;
	}
}
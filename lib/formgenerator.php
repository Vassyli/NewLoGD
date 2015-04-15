<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

class FormGenerator extends Datatypes {
	protected $action;
	protected $formtitle;
	protected $form_elements = array();
	
	public function __construct($formtitle, $action) {
		$this->action = $action;
		$this->formtitle = $formtitle;
	}
	
	public function getHtml() {
		$fields = "\n";
		
		foreach($this->form_elements as $name => $inp) {
			$type = "";
			$fields.= sprintf("    <div class=\"form-group%s\">\n",
				(array_sum($inp["errors"]) > 0 ? " form-errors" : "")
			);
			
			if($inp["type"] & self::TYPEGROUP_BUTTON) {
                $type = ($inp["type"] & self::TYPE_SUBMIT ? "submit" : "button");               
                $fields.= $this->generateButtonLayout($type, $name, $inp);
			}
			elseif($inp["type"] & self::TYPEGROUP_VARCHAR) {
                $type = ($inp["type"] & self::TYPE_EMAIL ? "email" : (
                    $inp["type"] & self::TYPE_PASSWORD ? "password" : "text"));
                
                $fields.= $this->generateVarcharLayout($type, $name, $inp);
			}
            elseif($inp["type"] & self::TYPE_FULLTEXT) {
                $fields.= $this->generateFulltextLayout($name, $inp);
            }
            elseif($inp["type"] & self::TYPE_BITFIELD) {
                $fields.= $this->generateBitfieldLayout($name, $inp);
            }

			$fields.= "    </div>\n";
		}
		
		return sprintf("<form action=\"%s\" method=\"post\">\n<fieldset>\n\t<legend>%s</legend>%s</fieldset></form>", $this->action, HTMLSpecialchars($this->formtitle), $fields);
	}
    
    protected function generateButtonLayout($type, $name, $info) {
        $esc_name = HTMLSpecialchars($name);
        $description = HTMLSpecialchars($info["description"]);
        $return = <<<HTML
        <div class="form-input skip-desc">
            <button type="{$type}" id="{$info["id"]}" name="{$esc_name}" value="{$info["value"]}">{$description}</button>
        </div>\n
HTML;
        return $return;
    }
    
    protected function generateVarcharLayout($type, $name, $info) {
        $esc_name = HTMLSpecialchars($name);
        $description = HTMLSpecialchars($info["description"]);
        $value = $info["value"]===NULL?"":' value="'.HTMLSpecialchars($info["value"]).'"';
        $required = empty($info["validator"]["required"])?"":" required";
        $maxlength = empty($info["validator"]["max-length"])?"":' maxlength="'.$info["validator"]["max-length"].'"';
        
        $return = <<<HTML
        <label for="{$info["id"]}">{$description}</label>
        <div class="form-input">
            <input type="{$type}" id="{$info["id"]}" name="{$esc_name}"{$value}{$required}{$maxlength}>
        </div>\n
HTML;
        return $return;
    }
    
    protected function generateFulltextLayout($name, $info) {
        $esc_name = HTMLSpecialchars($name);
        $description = HTMLSpecialchars($info["description"]);
        $value = $info["value"]===NULL?"":HTMLSpecialchars($info["value"]);
        $required = empty($info["validator"]["required"])?"":" required";
        $maxlength = empty($info["validator"]["max-length"])?"":' maxlength="'.$info["validator"]["max-length"].'"';
        
        $return = <<<HTML
        <label for="{$info["id"]}">{$description}</label>
        <div class="form-input">
            <textarea id="{$info["id"]}" name="{$esc_name}"{$required}{$maxlength}>{$value}</textarea>
        </div>\n
HTML;
        return $return;
    }
    
    protected function generateBitfieldLayout($name, $info) {
        $description = HTMLSpecialchars($info["description"]);
        $checkboxes = "";
        foreach($info["options"] as $key => $desc) {
            $checkboxes .= '<label><input type="checkbox" name="' 
                .HTMLSpecialchars($name)
                .'[]" value="'
                .HTMLSpecialchars($key)
                .'"'
                .($info["value"] & $key ? " checked" : "")
                .'>'
                .HTMLSpecialchars($desc)
                ."</label>\n            ";
        }
        
        $return = <<<HTML
        <div class="label">{$description}</div>
        <div class="form-input form-bitfield">
            {$checkboxes}
        </div>\n
HTML;
        return $return;
    }
	
	public function sanitize($values, $set_as_value = false) {
		$errors = 0;
		
		$debug = ("<b>Form-Validation</b>\n");
		
		foreach($this->form_elements as $name => $inp) {
			// Check if its not there
			if(!isset($values[$name]) and isset($inp["validator"]["required"]) and $inp["validator"]["required"] == true) {
				$debug .= (sprintf("\t[%s] not set, but it's required\n", $name));
				$errors++;
				$this->form_elements[$name]["errors"]["required"] = 1;
			}
			elseif(isset($values[$name])) {
				// Trim value
				$values[$name] = trim($values[$name]);
				
				$errors_before = $errors;
				
				// Validation depends always on the field type. For example, integer form fields need another validation/sanitation 
				//  than varchar fields.
				switch($inp["type"]) {
					case self::TYPE_SUBMIT:
                    case self::TYPE_RESET:
						// Button -  Do nothing, except removing the value from values
						unset($values[$name]);
						break;
						
					case self::TYPE_PASSWORD:
					case self::TYPE_LINE:
					case self::TYPE_EMAIL:
					default:
						// Text-Field use similar validators.
						
						// Validator-Check for email only
						if($inp["type"] == self::TYPE_EMAIL) {
							if(filter_var($values[$name], FILTER_VALIDATE_EMAIL) === false) {
								$debug .= (sprintf("\t[%s] is not a valid email address\n", $name));
								$errors++;
								$this->form_elements[$name]["errors"]["email"] = 1;
							}
						}
						
						// Check minimum length
						if(isset($inp["validator"]["min-length"])) {
							if(mb_strlen($values[$name]) < $inp["validator"]["min-length"]) {
								$debug .= (sprintf("\t[%s] is not long enough (Is: %s/%s)\n", $name, mb_strlen($values[$name]), $inp["validator"]["min-length"]));
								$errors++;
								$this->form_elements[$name]["errors"]["min-length"] = 1;
							}
						}
						
						// Check maximum length
						if(isset($inp["validator"]["max-length"])) {
							if(mb_strlen($values[$name]) > $inp["validator"]["max-length"]) {
								$debug .= (sprintf("\t[%s] is too long (Is: %s/%s)\n", $name, mb_strlen($values[$name]), $inp["validator"]["max-length"]));
								$errors++;
								$this->form_elements[$name]["errors"]["max-length"] = 1;
							}
						}
						
						// Check maximum byte length
						if(isset($inp["validator"]["max-bytelength"])) {
							if(strlen($values[$name]) > $inp["validator"]["max-bytelength"]) {
								$debug .= (sprintf("\t[%s] has too much bytes (Is: %s/%s)\n", $name, strlen($values[$name]), $inp["validator"]["max-bytelength"]));
								$errors++;
								$this->form_elements[$name]["errors"]["max-bytelength"] = 1;
							}
						}
						break;
				}
				
				// Some universal checks
				
				// Cross-Check: Check if another field contains the same value
				if(isset($inp["validator"]["crosscheck"])) {
					if($values[$name] != $values[$inp["validator"]["crosscheck"]]) {
						$debug .= (sprintf("\t[%s] does not match [%s]\n", $name, $inp["validator"]["crosscheck"]));
						$errors++;
						$this->form_elements[$name]["errors"]["crosscheck"] = 1;
						$this->form_elements[$inp["validator"]["crosscheck"]]["errors"]["crosscheck"] = 1;
					}
				}
				
				// Callback-Check: Call a callback function to check the input
				if(isset($inp["validator"]["callback"])) {
					$ret = call_user_func($inp["validator"]["callback"][0], $values[$name]);
					if($ret === false) {
						$debug .= (sprintf("\t[%s] does not pass callback\n", $name));
						$errors++;
						$this->form_elements[$name]["errors"]["callback"] = 1;
					}
				}
				
				if($errors_before === $errors and $inp["value"] !== NULL) {
					$this->form_elements[$name]["value"] = $values[$name];
				}
			}
			elseif(isset($inp["default"])) {
				// It isn't set, but it's not required - that's okay, we do not validate, but set the value to the default value.
				$values[$name] = $inp["default"];
			}
		}
		
		if($errors > 0) {
			$debug .= (sprintf("\t<b>Total FormErrors:</b> %s.\n", $errors));
			debug($debug);
			throw new Exception();
		}
		else {
			return $values;
		}
	}
	
	public function addInput($type, $desc, $name, $value, array $validator = [], array $options = []) {
		$this->form_elements[$name] = array(
			"id" => "id_".md5(microtime().$name),
			"type" => $type,
			"description" => $desc,
			"value" => $value,
			"validator" => $validator,
			"errors" => array(),
            "options" => $options,
		);
	}
	
	public function addLine($desc, $name, $value = "", array $validator = []) {
		$this->addInput(self::TYPE_LINE, $desc, $name, $value, $validator);
		return $this;
	}
	
	public function addPassword($desc, $name, array $validator = []) {
		$this->addInput(self::TYPE_PASSWORD, $desc, $name, NULL, $validator);
		return $this;
	}
	
	public function addEmail($desc, $name, $value = "", array $validator = []) {
		$this->addInput(self::TYPE_EMAIL, $desc, $name, $value, $validator);
		return $this;
	}
	
	public function addSubmitButton($desc, $name, $value) {
		$this->addInput(self::TYPE_SUBMIT, $desc, $name, $value, array());
	}
}
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
    
    protected $errors = 0;
    protected $debug = "";
    protected $values = [];
	
	public function __construct($formtitle, $action) {
		$this->action = $action;
		$this->formtitle = $formtitle;
	}
	
	public function getHtml() {
		$fields = "\n";
		
		foreach($this->form_elements as $name => $inp) {
			$type = "";
			$fields.= '    <div class="form-group'.(array_sum($inp["errors"]) > 0 ? " form-errors" : "")."\">\n";
			
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
		
		return '<form action="'. $this->action."\" method=\"post\">\n<fieldset>\n\t<legend>".HTMLSpecialchars($this->formtitle).'</legend>'.$fields.'</fieldset></form>';
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
        $this->errors = 0;
        $this->debug = [];
        $this->values = $values;
        $sanitized_values = [];
        
        foreach($this->form_elements as $field_name => $field_data) {
            if(isset($this->values[$field_name])) {
                // Clean values up: Trim, convert to int/float...
                $this->cleanupValue($field_name, $field_data);         
                $has_error = $this->validateValue($field_name, $field_data);
                
                if($has_error && $field_data["value"] !== NULL && isset($this->values[$field_name])) {
                    // If the value is incorret, we assign the faulty value to the form_data, except
                    //  if the value is explicit set to NULL (for example, passwords should not get 
                    //  printed in a HTML document).
					$this->form_elements[$field_name]["value"] = $this->values[$field_name];
				}
                
                if(!$has_error && isset($this->values[$field_name])) {
                    $sanitized_values[$field_name] = $this->values[$field_name];
                    
                    if($field_data["value"] !== NULL) {
                        $this->form_elements[$field_name]["value"] = $this->values[$field_name];
                    }
                }
            }
            else {
                if($this->isRequired($field_name, $field_data)) {
                    $this->addError($field_name, "required", sprintf("[%s] is not set, but it's required\n", $field_name));
                }
                else {
                    $sanitized_values[$field_name] = isset($field_data["default"]) 
                        ? $field_data["default"] 
                        : ($field_data["type"] & self::TYPEGROUP_NUMERIC  ? 0  : ""
                    );
                }
            }
        }
		
		/*foreach($this->form_elements as $name => $inp) {
			// Check if its not there
			if(!isset($values[$name]) and isset($inp["validator"]["required"]) and $inp["validator"]["required"] == true) {
				$debug .= (sprintf("\t[%s] not set, but it's required\n", $name));
				$errors++;
				$this->form_elements[$name]["errors"]["required"] = 1;
			}
			elseif(isset($values[$name])) {
                $this->trimValues($name, $inp);
				// Trim value
                if(is_array($values[$name])) {
                    $values[$name] = array_map("trim", $values[$name]);
                }
                else {
                    $values[$name] = trim($values[$name]);
                }
				
				$errors_before = $errors;
				
				// Validation depends always on the field type. For example, integer form fields need another validation/sanitation 
				//  than varchar fields.
				switch($inp["type"]) {
                    case self::TYPE_BITFIELD:
                        // Check if Bitfields are valid
                        $maxfield = 0;
                        $field = 0;
                        foreach($inp["options"] as $key => $desc) {
                            $maxfield |= intval($key);
                        }
                        
                        foreach($values[$name] as $val) {
                            $field |= intval($val);
                        }
                        
                        if($field > $maxfield or $field < 0) {
                            $debug .= (sprintf("\t[%s] has illegal arguments (max flags: %s, has flags: %s", $name, $maxfield, $field));
                            $errors++;
                            $this->form_elements[$name]["errors"]["bitfield"] = 1;
                        }
                        else {
                            $values[$name] = $field;
                        }
                        break;
                    
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
				
				if($errors_before === $errors and $inp["value"] !== NULL and isset($values[$name])) {
					$this->form_elements[$name]["value"] = $values[$name];
				}
			}
			elseif(isset($inp["default"])) {
				// It isn't set, but it's not required - that's okay, we do not validate, but set the value to the default value.
				$values[$name] = $inp["default"];
			}
		}*/
		
		if($this->errors > 0) {
			$debug .= (sprintf("\t<b>Total FormErrors:</b> %s.\n", $errors));
			debug($debug);
			throw new Exception();
		}
		else {
			return $sanitized_values;
		}
	}
    
    /*
     * Adds an error-Message if the Form is not valid, increases $this->errors and
     * marks the field as incorrect.
     */
    protected function addError($field_name, $type, $errmsg) {
        $this->errors++;
        array_push($this->debug, $errmsg);
        $this->form_elements[$field_name]["errors"][$type] = 1;
    }
    
    /**
     * This function validates a input field against its validators by calling
     * appropriate sub-routines depending on the field-type.
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     * @return bool false if is an error has occured, true if not.
     */
    protected function validateValue($field_name, $field_data) {
        $errors_before = $this->errors;
        
        switch($field_data["type"]) {
            case self::TYPE_SUBMIT:
            case self::TYPE_RESET:
                // Nothing to do
                break;
            
            case self::TYPE_BITFIELD:
                $this->validateBitfield($field_name, $field_data);
                break;
            
            case self::TYPE_PASSWORD:
            case self::TYPE_EMAIL:
            case self::TYPE_LINE:
            default:
                $this->validateVarchar($field_name, $field_data);
                break;
        }
        
        // Additional validations for every field
        // Cross-Check: Check if another field contains the same value
        if(!$this->validateAgainstCrosscheck($field_name, $field_data)) {
            $this->addError($field_name, "crosscheck", sprintf(
                "\t[%s] does not match [%s]\n", $field_name, $field_data["validator"]["crosscheck"]));
        }
        // Callback-Check: Call a callback function to check the input
        if(!$this->validateAgainstCallback($field_name, $field_data)) {
            $this->addError($field_name, "callback", sprintf(
                "\t[%s] does not pass callback\n", $field_name));
        }

        return ($errors_before <= $this->errors ? false : true);
    }
    
    /**
     * Validates a Bitfield-type field, primarly by checking if the flags it 
     * sets are valid. Does not yet supports "bitfield-holes"
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     */
    protected function validateBitfield($field_name, $field_data) {
        // Check if Bitfields are valid
        $maxfield = 0;
        $field = 0;
        foreach($field_data["options"] as $key => $desc) {
            $maxfield |= intval($key);
        }

        foreach($this->values[$field_name] as $val) {
            $field |= $val;
        }

        if($field > $maxfield or $field < 0) {
            $this->addError($field_name, "bitfield", 
                sprintf("\t[%s] has illegal arguments (max flags: %s, has flags: %s", $field_name, $maxfield, $field)
            );
        }
        else {
            $this->values[$field_name] = $field;
        }
    }
    
    /**
     * Validates a Varchar-type field (line, email, password) by using different 
     *   appropriate validators requested by $field_data.
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     */
    protected function validateVarchar($field_name, $field_data) {
        // If the varchar-Field should be an email, check if it really is one.
        if($field_data["type"] == self::TYPE_EMAIL) {
            if(!$this->validateAgainstEmail($field_name)) {
                $this->addError($field_name, "email", sprintf(
                    "\t[%s] is not a valid email address\n", $field_name));
            }
        }
        
        if(!$this->validateAgainstMinLength($field_name, $field_data)) {
            $this->addError($field_name, "min-length", sprintf(
                "\t[%s] is not long enough (Is: %s/%s)\n", 
                $field_name, mb_strlen($this->values[$field_name]), $field_data["validator"]["min-length"]));
        }
        
        if(!$this->validateAgainstMaxLength($field_name, $field_data)) {
            $this->addError($field_name, "max-length", sprintf(
                "\t[%s] is not long enough (Is: %s/%s)\n", 
                $field_name, mb_strlen($this->values[$field_name]), $field_data["validator"]["min-length"]));
        }
        
        if(!$this->validateAgainstMaxBytelength($field_name, $field_data)) {
            $this->addError($field_name, "min-length", sprintf(
                "\t[%s] has too much bytes (Is: %s/%s)\n", 
                $field_name, strlen($this->values[$field_name]), $field_data["validator"]["max-bytelength"]));
        }
    }
    
    /**
     * Checks if a given field is the same as an other field
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     * @return boolean true if validator is not set or if input is valid
     */
    protected function validateAgainstCrosscheck($field_name, $field_data) {
        if(isset($field_data["validator"]["crosscheck"])) {
            return ($this->values[$field_name] != $this->values[$field_data["validator"]["crosscheck"]]);
        }
        else { return true; }
    }
    
    /**
     * Checks the input of a field against a callback function, for example to check wheter a username 
     *   is already in use.
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     * @return boolean true if validator is not set or if input is valid
     */
    protected function validateAgainstCallback($field_name, $field_data) {
        if(isset($field_data["validator"]["callback"])) {
            return (call_user_func($field_data["validator"]["callback"][0], $this->values[$field_name]));
        }
        else { return true; }
    }
    
    /**
     * Checks if a given field has enough characters (using mb_strlen)
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     * @return boolean true if validator is not set or if input is valid
     */
    protected function validateAgainstMinLength($field_name, $field_data) {
        if(isset($field_data["validator"]["min-length"])) {
            return (mb_strlen($this->values[$field_name]) < $field_data["validator"]["min-length"] ? false : true);
        }
        else { return true; }
    }
    
    /**
     * Checks if a given field has not too much characters (using mb_strlen)
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     * @return boolean true if validator is not set or if input is valid
     */
    protected function validateAgainstMaxLength($field_name, $field_data) {
        if(isset($field_data["validator"]["max-length"])) {
            return (mb_strlen($this->values[$field_name]) > $field_data["validator"]["max-length"] ? false : true);
        }
        else { return true; }
    }
    
    /**
     * Checks if a given field has not too much bytes (using strlen(!))
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     * @return boolean true if validator is not set or if input is valid
     */
    protected function validateAgainstMaxBytelength($field_name, $field_data) {
        if(isset($field_data["validator"]["max-bytelength"])) {
            return (strlen($this->values[$field_name]) > $field_data["validator"]["max-bytelength"] ? false : true);
        }
        else { return true; }
    }
    
    /**
     * Checks if a given field is indead an email address
     * @param string $field_name Fieldname
     * @return boolean true if validator is not set or if input is valid
     */
    protected function validateAgainstEmail($field_name) {
        return filter_var($this->values[$field_name], FILTER_VALIDATE_EMAIL) === false ? false : true;
    }
    
    /**
     * Cleans an input value - it gets trimmed
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     */
    protected function cleanupValue($field_name, $field_data) {
        if(is_array($this->values[$field_name])) {
            if($field_data["type"] & self::TYPEGROUP_MULTIPLE) {
                // The value should only be an array of the the fieldtype is a TYPEGROUP_MULTIPLE
                $this->values[$field_name] = array_map("trim", $this->values[$field_name]);
                
                if($field_data["type"] & self::TYPE_BITFIELD) {
                    $this->values[$field_name] = array_map("intval", $this->values[$field_name]);
                }
            }
            else {
                // Invalid input - we have not expected an array.
                $this->values[$field_name] = "";
            }
        }
        else {
            if($field_data["type"] & self::TYPEGROUP_MULTIPLE) {
                $this->values[$field_name] = [trim($this->values[$fieldname])];
            }
            elseif($field_data["type"] & self::TYPEGROUP_INT) {
                $this->values[$field_name] = intval($this->values[$field_name]);
            }
            elseif($field_data["type"] & self::TYPEGROUP_FLOAT) {
                $this->values[$field_name] = (float)$this->values[$field_name];
            }
            elseif($field_data["type"] & self::TYPEGROUP_BUTTON) {
                unset($this->values[$field_name]);
            }
            else {
                $this->values[$field_name] = trim($this->values[$field_name]);
            }
        }
    }
    
    protected function isRequired($field_name, $field_data) {
        if(isset($field_data["validator"]["required"]) && $field_data["validator"]["required"] == true) {
            return true;
        }
        else {
            return false;
        }
    }
    
    protected function hasField($field_name) {
        if(isset($this->form_elements[$field_name])) { return true; }
        else { return false; }
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
		$this->addInput(self::TYPE_SUBMIT, $desc, $name, $value, []);
	}
}
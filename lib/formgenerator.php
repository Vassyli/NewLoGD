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
    
    protected $model = NULL;
	
	public function __construct($formtitle, $action) {
		$this->action = $action;
		$this->formtitle = $formtitle;
	}
    
    public function setModel(\Model $model) {
        $this->model = $model;
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
            elseif($inp["type"] & self::TYPE_FOREIGN) {
                $fields.= $this->generateForeignLayout($name, $inp);
            }
            elseif($inp["type"] & self::TYPE_INTRANGE) {
                $fields.= $this->generateIntRangeLayout($name, $inp);
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
    
    protected function generateIntRangeLayout($name, $info) {
        $esc_name = HTMLSpecialchars($name);
        $description = HTMLSpecialchars($info["description"]);
        $value = $info["value"]===NULL?"value=\"0\"":"value=\"".intval($info["value"])."\"";
        $required = empty($info["validator"]["required"])?"":" required";
        var_dump($info);
        $min = intval($info["options"]["range"]["min"]);
        $max = intval($info["options"]["range"]["max"]);
        $steps = intval($info["options"]["range"]["steps"]);
        
        $return = <<<HTML
        <label for="{$info["id"]}">{$description}</label>
        <div class="form-input">
            <input class="range-input" type="range" id="{$info["id"]}" name="{$esc_name}"{$value}{$required} min="{$min}" max="{$max}" steps="{$steps}">
            <output class="range-output" for="{$info["id"]}"></output>
        </div>\n
HTML;
        return $return;
    }
    
    protected function generateBitfieldLayout($name, $info) {
        $description = HTMLSpecialchars($info["description"]);
        $checkboxes = "";
        foreach($info["options"]["flags"] as $key => $desc) {
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
    
    protected function generateForeignLayout($name, $info) {
        $description = HTMLSpecialchars($info["description"]);
        $options = empty($info["validator"]["nullifempty"]) ? "" : (
            $info["value"] == NULL ? "<option selected value=\"\">NULL</option>" : "<option value=\"\">NULL</option>"
        );
        $name_esc = HTMLSpecialchars($name);
        
        if(!is_null($this->model) and isset($info["options"]["foreign"])) {
            $res = $this->model->get($info["options"]["foreign"]["table"])->all();
            foreach($res as $row) {
                if(isset($info["options"]["foreign"]["limit"])) {
                    $limit_method = $info["options"]["foreign"]["limit"];
                    // Continue loop if its not parantable
                    if($row->$limit_method() == false) {
                        continue;
                    }
                }
                
                $value_method = "get".$info["options"]["foreign"]["key"];
                $value = HTMLSpecialchars($row->$value_method());
                $desc = "";

                foreach($info["options"]["foreign"]["display"] as $d) {
                    $m = "get".$d;
                    $v = HTMLSpecialchars($row->$m());
                    $desc .= $v." - ";
                }
                $desc = substr($desc, 0, -3);
                if($value == $info["value"]) {
                    $options .= "<option selected value=\"{$value}\">{$desc}</option>";
                }
                else {
                    $options .= "<option value=\"{$value}\">{$desc}</option>";
                }
            }
        }
        
        $return = <<<HTML
        <div class="label">{$description}</div>
        <div class="form-input form-bitfield"><select name="{$name_esc}">
            {$options}
        </select></div>\n
HTML;
        return $return;
    }
	
    /**
     * Sanitizes all values in $values against the validation boundaries of the corresponding form element 
     * returns an array containing only _valid_ fields (even if the client sends additional, not existing fields)
     * 
     * @param array $values An associative array of the type $elementname => $elementvalue
     * @return array A sanitized array of the type $elementname => $elementvalue
     * @throws Exception if an validator fails
     */
	public function sanitize($values) {
        $this->errors = 0;
        $this->debug = [];
        $this->values = $values;
        $sanitized_values = [];
        
        foreach($this->form_elements as $field_name => $field_data) {
            if(array_key_exists($field_name, $this->values)) {
                // Clean values up: Trim, convert to int/float...
                $this->cleanupValue($field_name, $field_data);         
                $has_error = $this->validateValue($field_name, $field_data);
                
                if($has_error && $field_data["value"] !== NULL && array_key_exists($field_name, $this->values)) {
                    // If the value is incorret, we assign the faulty value to the form_data, except
                    //  if the value is explicit set to NULL (for example, passwords should not get 
                    //  printed in a HTML document).
					$this->form_elements[$field_name]["value"] = $this->values[$field_name];
				}
                
                
                if(!$has_error && array_key_exists($field_name, $this->values)) {
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
		
		if($this->errors > 0) {
			//$debug .= (sprintf("\t<b>Total FormErrors:</b> %s.\n", $errors));
			debug(implode("<br />", $this->debug));
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
            
            case self::TYPE_FOREIGN:
                $this->validateForeignField($field_name, $field_data);
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
        foreach($field_data["options"]["flags"] as $key => $desc) {
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
     * Validates a Foreign-Key-Field.
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     */
    protected function validateForeignField($field_name, $field_data) {
        $this->validateNullIfEmpty($field_name, $field_data);
        
        if(isset($field_data["options"]["foreign"]["check"]) && $this->values[$field_name] != NULL) {
            $m = $field_data["options"]["foreign"]["check"];
            $res = $this->model->get($field_data["options"]["foreign"]["table"])->$m($this->values[$field_name]);
            if($res === false) {
                $this->addError($field_name, "email", sprintf(
                    "\t[%s] is not a valid foreign key\n", $field_name));
            }
        }
        
        if(isset($field_data["options"]["foreign"]["limit"]) && $this->values[$field_name] != NULL && $res !== false) {
            $m = $field_data["options"]["foreign"]["limit"];
            if($res->$m() === false) {
                $this->addError($field_name, "email", sprintf(
                    "\t[%s] is not a valid foreign key\n", $field_name));
            }
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
     * 
     */
    protected function validateNullIfEmpty($field_name, $field_data) {
        if(!empty($field_data["validator"]["nullifempty"])) {
            $this->values[$field_name] = empty($this->values[$field_name]) ? NULL : $this->values[$field_name];
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
            return ($this->values[$field_name] == $this->values[$field_data["validator"]["crosscheck"]]);
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
    
    /**
     * Checks if a Field is required.
     * @param string $field_name Fieldname
     * @param array $field_data Fielddata-Array
     * @return boolean true if it's required, false if not.
     */
    protected function isRequired($field_name, $field_data) {
        if(isset($field_data["validator"]["required"]) && $field_data["validator"]["required"] == true) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Checks if the instance of FormGenerator has a Field named by the given parameter
     * @param string $field_name Fieldname
     * @return boolean
     */
    protected function hasField($field_name) {
        if(isset($this->form_elements[$field_name])) { return true; }
        else { return false; }
    }
	
    /**
     * Adds an input field
     * 
     * This function adds any input field supported by self::TYPE_*. The field gets 
     * identified by $name, which has to be used in order to drop/modify/check an existing
     * field.
     * 
     * The supported (custom) validator types are:
     *   - required => boolean, true if the field is required (TYPE_ANY)
     *   - max-bytelength => int, a maximum byte length (TYPEGROUP_VARCHAR), uses strlen to count.
     *   - max-length => int, a maximum character length (TYPEGROUP_VARCHAR), uses mb_strlen to count.
     *   - min-length => int, a minimum character length (TYPEGROUP_VARCHAR), uses mb_strlen to count
     *   - callback => [callback, string], a callback function supported by call_user_func() and a error message.
     *   - crosscheck => string $name, checks the field if it contains the same as the field given by the argument $name.
     * 
     * @param int $type The Type if the field. Use self::TYPE_* constants
     * @param string $desc A human readable description of the field. Like "First Name"
     * @param string $name The HTML name identifier of the Field (<input name="...">)
     * @param string $value A default value. If its set to NULL, the field does not get again filled after an error.
     * @param array $validator An array of validatortype => validatorargument
     * @param array $options An array with additional options if the Fieldtype requires it, eg a Bitfield
     * @return self
     */
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
        return $this;
	}	
    /**
     * Adds a text input line (<input type="text">)
     * @see self::addInput()
     */
	public function addLine($desc, $name, $value = "", array $validator = []) {
		$this->addInput(self::TYPE_LINE, $desc, $name, $value, $validator);
		return $this;
	}
	/**
     * Adds a password input  line (<input type="password">)
     * @see self::addInput()
     */
	public function addPassword($desc, $name, array $validator = []) {
		$this->addInput(self::TYPE_PASSWORD, $desc, $name, NULL, $validator);
		return $this;
	}
	/**
     * Adds an email input line (<input type="email">)
     * @see self::addInput()
     */
	public function addEmail($desc, $name, $value = "", array $validator = []) {
		$this->addInput(self::TYPE_EMAIL, $desc, $name, $value, $validator);
		return $this;
	}
	/**
     * Adds a submit button (<button type="submit"></button>)
     * @see self::addInput()
     */
	public function addSubmitButton($desc, $name, $value) {
		$this->addInput(self::TYPE_SUBMIT, $desc, $name, $value, []);
	}
}
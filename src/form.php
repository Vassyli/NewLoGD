<?php
/**
 * src/form.php - A form builder
 * 
 * @author Basilius Sauter
 * @package NewLoGD
 */

namespace NewLoGD;

use NewLoGD\i18n;

/**
 * NewLoGD\Form is a form builder class
 * 
 * NewLoGD\Form is a easy-to-use interface to build forms from PHP methods which 
 * also does data validation.
 * @param string $target Target route to post the form
 */
class Form implements \JsonSerializable {
    protected $formdata = [
        "method" => "POST",
        "target" => "",
        "form" => [
        ],
    ];
    
    protected $validated = NULL;
    protected $raw_results = [];
    protected $results = [];
    
    protected $validation_mapping = [
        "minlength" => "validateMinLength",
        "maxlength" => "validateMaxLength",
    ];
    
    protected $validation_errors = [];
    
    public function __construct($target) {
        $this->formdata["target"] = $target;
    }
    
    /**
     * Form data
     * @return array
     */
    public function jsonSerialize() : array {
        return $this->formdata;
    }
    
    public function setResults(array $raw_results) : self {
        $this->raw_results = $raw_results;
        return $this;
    }
    
    public function getResults() : array {
        if($this->validated === null) {
            throw new \Exception("[NewLoGD\\Form] Cannot return result without validation.");
        }
        if($this->validated === false) {
            throw new \Exception("[NewLoGD\\Form] Cannot return faulty results.");
        }
        
        return $this->results;
    }
    
    public function isValid() {
        if($this->validated === null) {
            $this->validate();
        }
        
        return $this->validated;
    }
    
    public function validate() : self {
        $errors = [];
        
        foreach($this->formdata["form"] as $name => $data) {
            // Check if a field is required - add error and skip validation is it isn't set, but must.
            if(!isset($this->raw_results[$name]) and !empty($data["options"]["required"])) {
                $errors[$name] = "Is required";
                continue;
            }
            
            $result = $this->castValue($this->raw_results[$name]??"", $data["options"]["cast"]??"");
            
            // Add casted value to the whitelisted values
            $this->results[$name] = $result;
            
            // Nothing to validate - skip this field
            if(!isset($data["options"]["validate"])) {
                continue;
            }
            
            // Validation
            foreach($data["options"]["validate"] as $key => $arguments) {
                
                // Unknown validation scheme - error
                if(!isset($this->validation_mapping[$key])) {
                    throw new \Exception("Validation " . $key . " does not exist.");
                }
                
                // Checks if the function passes the test
                $args = is_array($arguments) ? array_merge($result, $arguments) : [$result, $arguments];
                $passed = call_user_func_array([$this, $this->validation_mapping[$key]], $args);
                
                // passed - continue
                if($passed === true) {
                    continue;
                }
                // Not passed, no error so far - add to list
                elseif(isset($errors[$name])) {
                    $errors[$name] = [];
                }
                
                $errors[$name][] = $key;
            }
        }
        
        if(count($errors) > 0) {
            $this->validated = false;
        }
        else {
            $this->validated = true;
        }
        
        $this->validation_errors = $errors;
            
        return $this;
    }
    
    public function getValidationErrors() : array {
        if($this->validated === NULL) {
            throw new \Exception("[NewLoGD\\Form] getValidationErrors can only be called is the form was validated!");
        }
        
        return $this->validation_errors;
    }
    
    protected function checkName($name) { 
        if(isset($this->formdata["form"][$name])) {
            throw new \Exception("[NewLoGD\\Form] A form entry with the name $name already exists.");
        }
    }
    
    public function title(string $title) : self {
        $this->formdata["title"] = $title;
        return $this;
    }
    
    public function varchar(string $name, string $label, array $options = []) : self {
        $this->checkName($name);
        $this->formdata["form"][$name] = [
            "type" => "varchar",
            "label" => $label,
            "options" => $options,
        ];
        
        return $this;
    }
    
    protected function castValue($input, string $castTo) {
        switch($castTo) {
            case "int":
                if(is_numeric($input)) {
                    return (int)$input;
                }
                else {
                    return 0;
                }
                break;
            default:
                return is_array($input) ? implode("", $input) : $input;
        }
    }
    
    protected function validateMinLength(string $input, int $minLength) {
        return mb_strlen($input) >= $minLength;
    }
    
    protected function validateMaxLength(string $input, int $maxLength) {
        return mb_strlen($input) <= $maxLength;
    }
}
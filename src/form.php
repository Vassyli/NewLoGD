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
    /** @var array Array containing the form data */
    protected $formdata = [
        "method" => "POST",
        "target" => "",
        "form" => [
        ],
    ];
    
    /** @var NULL|bool NULL if form has yet been validated, True if it is ok, False if not. */
    protected $validated = NULL;
    /** @var array The raw, uncleaned data, for example the content of the $_POST array. */
    protected $raw_results = [];
    /** @var array The cleaned and sanitized and casted form values ready to use */
    protected $results = [];
    
    /** @var array An associative array where the key is a identifier for a validator and val is the method of this class */
    protected $validation_mapping = [
        "minlength" => "validateMinLength",
        "maxlength" => "validateMaxLength",
    ];
    
    /** @var array a lits of validation errors */
    protected $validation_errors = [];
    
    /** The constructor */
    public function __construct($target) {
        $this->formdata["target"] = $target;
    }
    
    /**
     * Returns the form data
     * @return array
     */
    public function jsonSerialize() : array {
        return $this->formdata;
    }
    
    /** 
     * Fills this class with some raw results (for example via $_POST)
     * @param array $raw_results The raw results 
     * @return self   
     */
    public function setResults(array $raw_results) : self {
        $this->raw_results = $raw_results;
        return $this;
    }
    
    /**
     * Returns validated, sanitized and casted results ready to use.
     * @return array The results
     * @throws \Exception Gets thrown if no validation has been done or if the validation failed.
     */
    public function getResults() : array {
        if($this->validated === null) {
            throw new \Exception("[NewLoGD\\Form] Cannot return result without validation.");
        }
        
        if($this->validated === false) {
            throw new \Exception("[NewLoGD\\Form] Cannot return faulty results.");
        }
        
        return $this->results;
    }
    
    /**
     * Returns true if form is valid or else false. If no validation has been done,
     * it calles automatically the validate() method.
     * @return bool True if validation was successful, else false.
     */
    public function isValid() {
        if($this->validated === null) {
            $this->validate();
        }
        
        return $this->validated;
    }
    
    /**
     * This method does the actual
     * @return self
     * @throws \Exception Gets thrown if a validation does not exist.
     */
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
    
    /**
     * Checks if a name within the form is already in use
     * @param type $name The name to look up
     * @throws \Exception Thrown if the name is already in use
     */
    protected function checkName($name) { 
        if(isset($this->formdata["form"][$name])) {
            throw new \Exception("[NewLoGD\\Form] A form entry with the name $name already exists.");
        }
    }
    
    /**
     * Sets the form title
     * @param string $title The title
     * @return self This object
     */
    public function title(string $title) : self {
        $this->formdata["title"] = $title;
        return $this;
    }
    
    /**
     * Creates a line field
     * @param string $name Element name
     * @param string $label Label of the element
     * @param array $options Various options
     * @return self This object
     */
    public function varchar(string $name, string $label, array $options = []) : self {
        $this->checkName($name);
        $this->formdata["form"][$name] = [
            "type" => "varchar",
            "label" => $label,
            "options" => $options,
        ];
        
        return $this;
    }
    
    /**
     * Converts a form value to an appropriate PHP type
     * @param type $input the input value
     * @param string $castTo what to cast to
     * @return mixed The casted value.
     */
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
    
    /**
     * Checks if a value is at least $minLength characters long
     * @param string $input Value to check
     * @param int $minLength Minimum string length
     * @return bool True if validation is successful, else false.
     */
    protected function validateMinLength(string $input, int $minLength) {
        return mb_strlen($input) >= $minLength;
    }
    
    /**
     * Checks if a value is at max $maxLength characters long
     * @param string $input Value to check
     * @param int $maxLength Maximum string length
     * @return bool True if validation is successful, else false.
     */
    protected function validateMaxLength(string $input, int $maxLength) {
        return mb_strlen($input) <= $maxLength;
    }
}
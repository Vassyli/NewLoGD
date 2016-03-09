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
        ];
        
        return $this;
    }
}
<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Submodel\Item;

class TableFieldItem implements \Truemodelitem {
    private $model;
    
    const FIELD_PRIMARYID    = "id";
    const FIELD_TABLES_ID    = "tables_id";
    const FIELD_FIELDNAME    = "fieldname";
    const FIELD_FIELDTYPE    = "fieldtype";
    const FIELD_DEFAULTVALUE = "default_value";
    const FIELD_DESCRIPTION  = "description";
    const FIELD_PROPERTIES   = "properties";
    const FIELD_OPTIONS      = "options";
    
    public function __construct(\Model $model) {
        $this->model = $model;
    }
    
    public function __set($name, $value) {
        switch($name) {
			// Integers
			case self::FIELD_PRIMARYID:
            case self::FIELD_TABLES_ID:
            case self::FIELD_FIELDTYPE:
				$this->$name = ($value === NULL) ? NULL : intval($value);
				break;
            
            // JSON
            case self::FIELD_PROPERTIES:
            case self::FIELD_OPTIONS:
                $this->$name = json_decode($value, true);
                break;
			
			// String Rest
			default:
				$this->$name = $value;
				break;
		}
    }
    
    public function getId() { return $this->id; }
    public function getTablesId() { return $this->tables_id; }
    public function getFieldname() {return $this->fieldname; }
    public function getFieldtype() { return $this->fieldtype; }
    public function getDefaultValue() { return $this->default_value;}
    public function getDescription() { return $this->description; }
    public function getProperties() { return $this->properties; }
    public function getProperty($key, $default = "") { 
        return isset($this->properties[$key]) ? $this->properties[$key] : $default;
    }
    public function getOptions() { return $this->options; }
    public function getOption($key, $default = "") {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }
}

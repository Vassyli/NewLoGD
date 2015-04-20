<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Query;

/**
 * Provides a query-class with tools to add values
 */
trait Writable {
    protected $fieldcount = 0;
	protected $staticfieldcount = 0;
    
    protected function addField($field) {
        $this->fieldcount++;
			
        if(is_array($field)) {
            array_push($this->fragments["FIELDS"], array(
                "fieldname" => $field[0],
                "static" => true,
                "static-value" => $field[1]
            ));

            $this->staticfieldcount++;
        }
        else {
            array_push($this->fragments["FIELDS"], array(
                "fieldname" => $field,
                "static" => false,
                "static-value" => NULL
            ));
        }
        
        return $this;
    }
    
    protected function addValue($value, $row = 0) {
        array_push($this->fragments["VALUES"][$row], $value);
        return $this;
    }
}

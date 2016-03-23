<?php

namespace Database;

/**
 * Character Properties
 * @Entity
 * @Table(name="Character_Properties")
 */
class CharacterProperty {
    /**
     * @var \Database\Character Owning Character
     * @Id @ManyToOne(targetEntity="Character")
     */
    private $character;
    /**
     * @var string Property Field Name
     * @Id @Column(type="string")
     */
    private $fieldname;
    /**
     * @var string Value Type (string, integer, decimal)
     * @Column(type="string")
     */
    private $value_type;
    /**
     * @Column(type="string", nullable=True)
     */
    private $value_string = NULL;
    /**
     * @Column(type="integer", nullable=True)
     */
    private $value_integer = NULL;
    /**
     * @Column(type="decimal", nullable=True)
     */
    private $value_decimal = NULL;
    
    public function setCharacter(Character $character) {
        $this->character  = $character;
    }
    
    public function getKey() {
        return $this->fieldname;
    }
    
    public function setKey(string $key) {
        $this->fieldname = $key;
    }
    
    public function getValue() {
        switch($this->value_type) {
            case "decimal": return $this->value_decimal;
            case "integer": return $this->value_integer;
            default: return $this->value_string;
        }
    }
    
    public function setValue($value) {
        if(is_int($value)) {
            $this->value_type = "integer";
            $this->value_integer = $value;
        }
        elseif(is_float($value)) {
            $this->value_type = "decimal";
            $this->value_decimal = $value;
        }
        else {
            $this->value_type = "string";
            $this->value_string = $value;
        }
    }
}
<?php
/**
 * database/user.php - Doctrine ORM class
 *
 * @author Basilius Sauter
 * @package Database
 */

namespace Database;

/**
 * ORM for Character table
 * @Entity
 * @Table(name="Characters")
 */
class Character {
    /**
	 * @Id @Column(type="integer") @GeneratedValue
	 */
    private $id;
    
    /**
     * @ManyToOne(targetEntity="User", inversedBy="characters")
     */
    private $owner;
    
    /**
	 * @Column(type="string")
	 */
    private $name;
    
    /**
	 * @Column(type="integer")
	 */
    private $level;
    
    public function getId() { return $this->id; }
    
    public function getOwner() { return $this->owner; }
    
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getLevel() {return $this->level; }
    public function setLevel($level) { $this->level = $level; }
    public function addLevel($level = 1) { $this->level+=$level; }
}
<?php
/**
 * database/user.php - Doctrine ORM class
 *
 * @author Basilius Sauter
 * @package Database
 */

namespace Database;

use NewLoGD\Application as App;

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
	 * @Column(type="integer", options={"default"=1})
	 */
    private $level = 1;
    
    /** 
     */
    private $scene = NULL;
    
    public function getId() { return $this->id; }
    
    public function getOwner() { return $this->owner; }
    public function setOwner(User $owner) { $this->owner = $owner; }
    
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getLevel() {return $this->level; }
    public function setLevel($level) { $this->level = $level; }
    public function addLevel($level = 1) { $this->level+=$level; }
    
    public function getScene() {
        if($this->scene === NULL) {
            $entityManager = App::getEntityManager();
            $this->scene = $entityManager->find("\\Database\\CharacterScene", $this->id);
        }
        
        return $this->scene;
    }
    public function setScene(CharacterScene $scene) {
        App::getEntityManager()->persist($scene);
        $this->scene = $scene;
    }
}
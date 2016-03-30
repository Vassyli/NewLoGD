<?php
/**
 * database/user.php - Doctrine ORM class
 *
 * @author Basilius Sauter
 * @package Database
 */

namespace Database;

use Doctrine\Common\Collections\ArrayCollection;

use NewLoGD\Application as App;

/**
 * ORM for Character table
 * @Entity
 * @Table(name="Characters")
 */
class Character {
    /**
     * @var int Primary user id
	 * @Id @Column(type="integer") @GeneratedValue
	 */
    private $id;
    
    /**
     * @var Database\User Owning User object
     * @ManyToOne(targetEntity="User", inversedBy="characters")
     */
    private $owner;
    
    /**
     * @var string Name of the character
	 * @Column(type="string")
	 */
    private $name;
    
    /**
     * @var int Level of the character
	 * @Column(type="integer", options={"default"=1})
	 */
    private $level = 1;
    
    /**
     * @var CharacterScene The current scene the character is viewing
     * @OneToOne(targetEntity="CharacterScene", mappedBy="character")
     */
    private $scene = NULL;
    
    /**
     * @var array<\Database\CharacterProperties> List of database characters
     * @OneToMany(targetEntity="CharacterProperty", mappedBy="character")
     */
    private $properties = [];
    private $_properties = NULL;
    
    public function __construct() {
        $this->properties = new ArrayCollection();
    }
    
    /**
     * Gets the primary id
     * @return int primary id
     */
    public function getId() { return $this->id; }
    
    /**
     * Returns the owner of this character
     * @return \Database\User The owner of this character
     */
    public function getOwner() { return $this->owner; }
    /**
     * Sets the ownership of this character to the given user
     * @param \Database\User $owner The user that is going to own this character
     */
    public function setOwner(User $owner) { $this->owner = $owner; }
    
    /**
     * Gets the name of the character
     * @return string Character name
     */
    public function getName() { return $this->name; }
    /**
     * Sets the name of the character
     * @param string $name Character name
     */
    public function setName(string $name) { $this->name = $name; }
    
    
    /**
     * Gets the character level
     * @return int Level
     */
    public function getLevel() {return $this->level; }
    /**
     * Sets the character level
     * @param int $level New Level
     */
    public function setLevel(int $level) { $this->level = $level; }
    /**
     * Increases the character level
     * @param int $level Levels to increase (negative for level decrease)
     */
    public function addLevel(int $level = 1) { $this->level+=$level; }
    
    /**
     * Returns the current scene of the character
     * @return \Database\CharacterScene The current Scene
     */
    public function getScene() {
        if($this->scene === NULL) {
            $entityManager = App::getEntityManager();
            $this->scene = $entityManager->find("\\Database\\CharacterScene", $this->id);
        }
        
        return $this->scene;
    }
    /**
     * Sets the current scene of the character
     * @param \Database\CharacterScene $scene The scene that this character has to own
     */
    public function setScene(CharacterScene $scene) {
        App::getEntityManager()->persist($scene);
        $this->scene = $scene;
    }
    public function switchScene(Scene $scene) {
        if($this->scene === NULL) {
            $charscene = new CharacterScene();
            $charscene->setCharacter($this);
            $this->setScene($charscene);
        }
        
        $this->scene->fillFromScene($scene);
    }
    
    public function get($fieldname, $default = NULL) {
        $this->loadProperties();
        
        return $this->_properties[$fieldname][1]??$default;
    }
    
    public function set($fieldname, $value) {
        $this->loadProperties();
        
        if(isset($this->_properties[$fieldname])) {
            // Key has already been set - change
            $this->_properties[$fieldname][1] = $value;
            $this->properties[$this->_properties[$fieldname][0]]->setValue($value);
        }
        else {
            // Key is not known - create it!
            $property = new CharacterProperty();
            $property->setCharacter($this);
            $property->setKey($fieldname);
            $property->setValue($value);
            App::getEntityManager()->persist($property);
            $this->properties->add($property);
            $this->_properties[$fieldname] = $value;
        }
    }
    
    private function loadProperties() {
        if($this->_properties === NULL) {
            $this->_properties = [];
            foreach($this->properties as $key => $val) {
                $this->_properties[$val->getKey()] = [$key, $val->getValue()];
            }
        }
    }
}
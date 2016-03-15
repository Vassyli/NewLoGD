<?php
/**
 * database/scene.php - Doctrine ORM class
 * @author Basilius Sauter
 * @package Database
 */

namespace Database;

/**
 * ORM for Scenes
 * @Entity
 * @Table(name="Character_Scenes")
 */
class CharacterScene {
    /** @Id @OneToOne(targetEntity="Character") */
    private $character;
    
    /** @Column(type="string") */
    private $title;
    
    /** @Column(type="text") */
    private $body;
    
    /** @return Character The instance of the owning Character */
    public function getCharacter() { return $this->character; }
    /** @param Character $character The owning Character */
    public function setCharacter(Character $character) {
        $this->character = $character;
    }
	
    /** @return string The Title of the Scene */
	public function getTitle() { return $this->title; }
    /** @param string $title The title of the Scene */
	public function setTitle($title){ $this->title = $title; }
	
    /** @return string Text describing the scene */
	public function getBody() { return $this->body; }
    /** @param string $body Text describing the scene */
	public function setBody($body) { $this->body = $body; }
}
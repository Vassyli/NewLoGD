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
    /** 
     * @var \Database\Character Character id
     * @Id @OneToOne(targetEntity="Character") 
     */
    private $character;
    
    /** 
     * @var string Scene Title
     * @Column(type="string") 
     */
    private $title;
    
    /** 
     * @var string Scene Description
     * @Column(type="text") 
     */
    private $body;
    
    /** 
     * Returns the character owning this scene
     * @return Character The instance of the owning Character 
     */
    public function getCharacter() { return $this->character; }
    /** 
     * Sets the character owning this scene
     * @param Character $character The owning Character 
     */
    public function setCharacter(Character $character) {
        $this->character = $character;
    }
	
    /** 
     * Gets the Scene title
     * @return string The Title of the Scene 
     */
	public function getTitle() { return $this->title; }
    /** 
     * Sets the title of the scene
     * @param string $title The title of the Scene 
     */
	public function setTitle($title){ $this->title = $title; }
	
    /** 
     * Gets the description of the scene
     * @return string Text describing the scene 
     */
	public function getBody() { return $this->body; }
    /** 
     * Sets the description of the scene
     * @param string $body Text describing the scene 
     */
	public function setBody($body) { $this->body = $body; }
}
<?php
/**
 * database/scene.php - Doctrine ORM class
 * @author Basilius Sauter
 * @package Database
 */

namespace Database;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ORM for Scenes
 * @Entity
 * @Table(name="Scenes")
 */
class Scene {
    /** 
     * @var int primary id
     * @Id @Column(type="integer") @GeneratedValue
     */
    private $id;
    
    /** 
     * @var string Title of the Scene
     * @Column(type="string") 
     */
    private $title;
    
    /** 
     * @var string Description of the Scene
     * @Column(type="text") 
     */
    private $body;
    
    /**
     * @var array List of actions
     * @OneToMany(targetEntity="SceneAction", mappedBy="scene", cascade={"persist"}, orphanRemoval=true)
     */
    private $actions;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->actions = new ArrayCollection();
    }
    
    /** 
     * Returns the primary id
     * @return int Primary id of db entry 
     */
    public function getId() { return $this->id; }
	
    /** 
     * Returns the title
     * @return string Title of the scene 
     */
	public function getTitle() { return $this->title; }
    /** 
     * Sets the title 
     * @param type $title Title of the scene 
     */
	public function setTitle($title){ $this->title = $title; }
	
    /** 
     * Returns the body
     * @return string Text describing the Scene 
     */
	public function getBody() { return $this->body; }
    /** 
     * Sets the body 
     * @param string $body Text describing the Scene 
     */
	public function setBody($body) { $this->body = $body; }
    
    /**
     * Returns a list of actions
     * @return array List of actions
     */
    public function getActions() { return $this->actions; }
    /**
     * Adds an action to this scene.
     * @param \Database\SceneAction $action The Character
     */
    public function addAction(SceneAction $action) {
        $this->actions->add($action);
        $action->setScene($this);
    }
}
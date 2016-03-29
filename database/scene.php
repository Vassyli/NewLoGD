<?php
/**
 * database/scene.php - Doctrine ORM class
 * @author Basilius Sauter
 * @package Database
 */

namespace Database;

use Doctrine\Common\Collections\ArrayCollection;

use NewLoGD\Helper\find;

/**
 * ORM for Scenes
 * @Entity
 * @Table(name="Scenes")
 */
class Scene {
    use find;
    
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
     * @OrderBy({"sorting" = "ASC"})
     */
    private $actions;
    /**
     * @var array List of Extensions
     * @OneToMany(targetEntity="SceneExtension", mappedBy="scene", cascade={"persist"}, orphanRemoval=true)
     */
    private $extensions;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->actions = new ArrayCollection();
        $this->extensions = new ArrayCollection();
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
	public function setBody($body) { $this->body = normalizeLineBreaks($body); }
    
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
    
    public function getExtensions() {
        return $this->extensions;
    }
}
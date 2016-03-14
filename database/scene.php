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
 * @Table(name="Scenes")
 */
class Scene {
    /**
	 * @Id @Column(type="integer") @GeneratedValue
	 */
    private $id;
    
    /**
	 * @Column(type="string")
	 */
    private $title;
    
    /**
	 * @Column(type="text")
	 */
    private $body;
    
    public function getId() { return $this->id; }
	
	public function getTitle() { return $this->title; }
	public function setTitle($title){ $this->title = $title; }
	
	public function getBody() { return $this->body; }
	public function setBody($body) { $this->body = $body; }
}
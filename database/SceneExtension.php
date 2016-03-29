<?php

namespace Database;

/**
 * Extension for a Scene
 * @Entity
 * @Table(name="Scene_Extensions")
 */
class SceneExtension {
    /**
     * @var Database\Scene Owning Scene
     * @Id @ManyToOne(targetEntity="Scene")
     */
    private $scene;
    /**
     * @var string Extension name 
     * @Id @Column(type="string")
     */
    private $extension;
    
    public function getScene() { return $this->scene; }
    
    public function getExtension() { return $this->extension; }
}
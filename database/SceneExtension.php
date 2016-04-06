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
    /**
     * @var array Extension configuration
     * @Column(type="json_array") 
     */
    private $config = [];
    
    public function getScene() { return $this->scene; }
    
    public function getExtension() { return $this->extension; }
    
    public function getConfig() : array { return $this->config; }
    public function setConfig(array $config) { $this->config = $config; }
    public function set($key, $value) { $this->config[$key] = $value; }
    public function get($key, $default = NULL) { return $this->config[$key]??$default;}
}
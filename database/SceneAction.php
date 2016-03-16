<?php
/**
 * database/SceneActions.php
 */

namespace Database;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ORM for SceneActions
 * @Entity
 * @Table(name="Scene_Actions")
 */
class SceneAction implements \JsonSerializable {
    /**
     * @var int primary id
     * @Id @Column(type="integer") @GeneratedValue
     */
    private $id;
    
    /**
     * @var \Database\Scene Parent Scene
     * @ManyToOne(targetEntity="Scene", inversedBy="actions")
     * @JoinColumn(name="scene", referencedColumnName="id", nullable=true)
     */
    private $scene = NULL;
    
    /**
     * @var \Database\SceneAction Parent Action
     * @ManyToOne(targetEntity="SceneAction", inversedBy="childs")
     * @JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    private $parent = NULL;
    
    /**
     * @var ArrayCollection list of Children Actions 
     * @OneToMany(targetEntity="SceneAction", mappedBy="parent")
     */
    private $childs;
    
    /**
     * @var type 
     * @Column(type="string")
     */
    private $title = "";
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->childs = new ArrayCollection();
    }
    
    /**
     * Returns the id
     * @return int The primary id
     */
    public function getId() { return $this->id; }
    
    /**
     * Returns either the parent scene or the parent SceneAction
     * @return type
     */
    public function getParent() {
        return $this->parent??$this->scene;
    }
    /**
     * Sets the parent of this action
     * @param type $parent
     * @throws \Exception If $parent has unexpected type
     */
    public function setParent($parent) {
        if($parent instanceof SceneAction) {
            $this->parent = $parent;
            $this->scene = NULL;
        }
        elseif($parent instanceof Scene) {
            $this->parent = NULL;
            $this->scene = $parent;
        }
        else {
            throw new \Exception("[Database\\SceneAction] setParent accepts only Database\\SceneAction or Database\\Scene as \$parent.");
        }
    }
    
    /**
     * Get action title
     * @return string Action Title
     */
    public function getTitle() { return $this->title; }
    /**
     * Set action title
     * @param string $title Action Title
     */
    public function setTitle(string $title) { $this->title = $title; }
    
    public function jsonSerialize() {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "childs" => $this->childs->toArray(),
        ];
    }
}
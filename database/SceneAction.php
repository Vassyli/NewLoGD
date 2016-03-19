<?php
/**
 * database/SceneActions.php
 */

namespace Database;

use Doctrine\Common\Collections\ArrayCollection;

use NewLoGD\Helper\find;

/**
 * ORM for SceneActions
 * @Entity
 * @Table(name="Scene_Actions")
 */
class SceneAction implements \JsonSerializable {
    use find;
    
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
     * @var string Action Title 
     * @Column(type="string")
     */
    private $title = "";
    
    /**
     * @var int Sorting weight - the bigger, the further down
     * @Column(type="integer", options={"default" = 0})
     */
    private $sorting = 0;
    
    /**
     * @var \Database\Scene Parent Scene
     * @OneToOne(targetEntity="Scene")
     * @JoinColumn(name="target_scene", referencedColumnName="id", nullable=true)
     */
    private $target_scene = NULL;
    
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
    
    /**
     * Get sorting weight
     * @return int Sorting weight
     */
    public function getSorting() : int { return $this->sorting; }
    /**
     * Set sorting weight
     * @param string $sort Sorting weight
     */
    public function setSorting(int $sorting) { $this->sorting = $sorting; }
    
    /**
     * Returns the target scene of the action
     * @return \Database\Scene|null NULL of no Scene or else the Scene.
     */
    public function getTargetScene() { return $this->target_scene; }
    /**
     * Sets the target scene.
     * @param \Database\Scene $target The target Scene
     */
    public function setTargetScene(Scene $target) {
        
    }
    
    public function jsonSerialize() {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "childs" => $this->childs->toArray(),
            "target" => is_null($this->target_scene) ? null : $this->target_scene->getId(),
        ];
    }
}
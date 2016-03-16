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
class SceneAction {
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
    private $scene;
    
    /**
     * @var \Database\SceneAction Parent Action
     * @ManyToOne(targetEntity="SceneAction", inversedBy="childs")
     * @JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    private $parent;
    
    /**
     * @var ArrayCollection list of Children Actions 
     * @OneToMany(targetEntity="SceneAction", mappedBy="parent")
     */
    private $childs;
    
    /**
     * Returns the id
     * @return int The primary id
     */
    public function getId() { return $this->id; }
}
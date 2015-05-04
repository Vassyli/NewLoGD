<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Navigation;

class CustomItem implements ItemAPI {
    private $model;
    
    protected $action;
    protected $title;
    
    static private $itemcount = 0;
    
    public function __construct($title, $action, $parent) {
        self::$itemcount--;
        
        $this->id = self::$itemcount;
        $this->parentid = $parent;
        $this->title = $title;
        $this->action = $action;
    }
    
    public function getId()       {return $this->id;}
	public function getParentid() {return NULL;}
	public function getAction()   {return $this->action;}
	public function getParsedAction()   {return $this->action;}
	public function getTitle()    {return $this->title;}
}
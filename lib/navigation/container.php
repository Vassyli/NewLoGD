<?php

namespace Navigation;

class Container implements \IteratorAggregate {
	/*
		$navs = array(
			0 => array(
				"childs" => array(
					id => array(
						"item" => instanceof \Navigation\ItemAPI(id)
					),
				),
			),
			id => array(
				"childs" => array(
					// etc
				),
				"item" => instanceof \Navigation\ItemAPI(id)
			),
		);
	*/
	protected $navs = array();
	
	public function __construct() {
		$this->navs = array(
			0 => array("childs" => array()),
		);
	}
	
	public function add_bulk($rows) {
		foreach($rows as $item) {
			// Actual Link without an action
			if($item->getParentid() === NULL and $item->getAction() !== NULL) {
				$this->navs[0]["childs"][$item->getId()] = array(
					"item" => $item,
				);
			}
			// Actual Link with a parent
			elseif($item->getParentid() !== NULL and $item->getAction() !== NULL) {
				if(empty($this->navs[$item->getParentid()])) {
					debug(sprintf("Navigation-Item with ID=%i has a parent assigned that does not exist.\n", $item->getId()));
				}
				else {
					$this->navs[$item->getParentid()]["childs"][$item->getId()] = array(
						"item" => $item,
					);
				}
			}
			// Link-Title - Let's ignore the parent
			elseif($item->getAction() === NULL) {
				$this->navs[$item->getId()] = array(
					"item" => $item,
					"childs" => array(),
				);
			}
		}
	}
	
	public function getIterator() {
        return new \ArrayIterator($this->navs);
    }
}

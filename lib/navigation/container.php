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
			if($item->get_parentid() === NULL and $item->get_action() !== NULL) {
				$this->navs[0]["childs"][$item->get_id()] = array(
					"item" => $item,
				);
			}
			// Actual Link with a parent
			elseif($item->get_parentid() !== NULL and $item->get_action() !== NULL) {
				if(empty($this->navs[$item->get_parentid()])) {
					debug(sprintf("Navigation-Item with ID=%i has a parent assigned that does not exist.\n", $item->get_id()));
				}
				else {
					$this->navs[$item->get_parentid()]["childs"][$item->get_id()] = array(
						"item" => $item,
					);
				}
			}
			// Link-Title - Let's ignore the parent
			elseif($item->get_action() === NULL) {
				$this->navs[$item->get_id()] = array(
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

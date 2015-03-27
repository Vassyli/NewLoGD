<?php

namespace page;

use \Navigation;

abstract class Base implements api, \Basicmodelitem {
	const IS_EDITABLE  = 0b000000001;
	const IS_DELETABLE = 0b000000010;
	
	private $model;
	
	protected $id = 0;
	protected $type = "";
	protected $action = "";
	protected $title = "";
	protected $subtitle = "";
	protected $content = "";
	
	protected $arguments = array();

	public function __construct($model, $row) {
		$this->model = $model;
		
		$this->id = $row['id'];
		$this->type = $row['type'];
		$this->action = $row['action'];
		$this->title = $row['title'];
		$this->subtitle = $row['subtitle'];
		$this->content = $row['content'];
	}
	
	public function set_arguments($args) {
		$this->arguments = $args;
	}
	
	public function get_id() { return $this->id; }
	public function get_type() { return $this->type; }
	public function get_action() { return $this->action; }
	public function get_title() { return $this->title; }
	public function get_subtitle() { return $this->subtitle; }
	
	public function get_navigation() {
		$container = new Navigation\Container();
		$container->add_bulk($this->model->get("Navigations")->getby_pageid($this->get_id()));
		return $container;
	}
}
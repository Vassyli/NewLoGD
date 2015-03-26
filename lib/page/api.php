<?php

namespace page;

interface api {
	//public function __construct($model, $row);
	public function set_arguments($args);
	public function execute();
	
	public function get_title();
	public function get_subtitle();
	public function get_content();
	public function get_navigation();
}
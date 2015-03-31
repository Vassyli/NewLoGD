<?php

namespace page;

use \Navigation;

class Node extends Base {
	protected $parser = NULL;
	
	public function __construct($model, $row) {
		parent::__construct($model, $row);
		
		$this->parser = new \Parser();
	}
	
	public function get_navigation() {
		$container = new Navigation\Container();
		$container->add_bulk($this->model->get("Navigations")->getby_pageid($this->get_id()));
		return $container;
	}
	
	public function get_parsed_content() {
		$content = $this->get_content();
		
		if($this->keep_html() === false) {
			$content = HTMLSpecialchars($content, ENT_HTML5, LOGD_ENCODING);
		}
		
		if($this->use_parser() === true) {
			$content = $this->parser->parse($content);
		}
		
		return $content;
	}
}
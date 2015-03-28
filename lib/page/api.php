<?php

namespace page;

interface api {
	const FLAG_IS_EDITABLE  = 0b000000001;
	const FLAG_IS_DELETABLE = 0b000000010;
	const FLAG_NO_PARSE     = 0b000010000;
	const FLAG_KEEP_HTML    = 0b000100000;
	
	//public function __construct($model, $row);
	public function set_arguments($args);
	public function execute();
	
	public function get_title();
	public function get_subtitle();
	public function get_action();
	public function get_content();
	public function get_navigation();
	
	public function get_flags();
	public function is_editable();
	public function is_deletable();
	public function use_parser();
	public function keep_html();
	
}
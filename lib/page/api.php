<?php

namespace page;

/**
 * Provides an interface for all Page-Modules
 */
interface api {
	/** @var int Notates if a page is editable. */
	const FLAG_IS_EDITABLE  = 0b000000001; //   1
	/** @var int Notates if a page is deletable. */
	const FLAG_IS_DELETABLE = 0b000000010; //   2
	/** @var int Notates of a page does not want to use the parser. */
	const FLAG_NO_PARSE     = 0b000010000; //  16 
	/** @var int Notates if a page wants to keep it's HTML. */
	const FLAG_KEEP_HTML    = 0b000100000; //  32
	/** @var int Notates if a page wants to use default output handler */
	const FLAG_HAS_NO_OUTPUT= 0b100000000; // 256
	
	//public function __construct($model, $row);
	/**
	 * Sets additional arguments to the page.
	 * 
	 * @param array $args sets additional arguments
	 */
	public function set_arguments(array $args);
	
	/**
	 * Executes all controller code
	 */
	public function execute();
	
	/**
	 * Initiate everything
	 */
	public function initiate();
	
	/**
	 * Returns the ID of the page
	 * 
	 * @return string ID of the page
	 */
	public function get_id();
	
	/**
	 * Returns the type of the page
	 * 
	 * @return string Node of the page
	 */
	public function get_type();
	
	/**
	 * Returns the title of the page
	 * 
	 * @return string Title of the page
	 */
	public function get_title();
	
	/**
	 * Returns the subtitle of the page
	 * 
	 * @return string Subtitle of the page
	 */
	public function get_subtitle();
	
	/**
	 * Returns the action of the page
	 * 
	 * @return string action of the page
	 */
	public function get_action();
	
	/**
	 * Returns the content of the page
	 * 
	 * @return string Content of the page
	 */
	public function get_content();
	
	/**
	 * Preload the navigation
	 */
	public function load_navigation();
	
	/**
	 * Returns the title of the page
	 * 
	 * @return \Navigation\Container Container containing the navigation
	 */
	public function get_navigation();
	
	/*
	 * Returns the flag-field
	 * 
	 * @return int All flags
	 */
	public function get_flags();
	
	/**
	 * Checks if the page is editable
	 * 
	 * @return bool true if the page is editable, false if not.
	 */
	public function is_editable();
	
	/**
	 * Checks if the page is deletable
	 * 
	 * @return bool true if the page is deletable, false if not.
	 */
	public function is_deletable();
	
	/**
	 * Checks if the page wants to use the parser (Checks the absence of self::FLAG_NO_PARSE)
	 * 
	 * @return bool true if the page is uses the parser, false if not.
	 */
	public function use_parser();
	
	/**
	 * Checks if the page wants to keep it's HTML
	 * 
	 * @return bool true if the page wants to keep the HTML, false if not.
	 */
	public function keep_html();
	
	/**
	 * Checks if a page wants to use default output handler
	 * 
	 * @return bool true if a page wants to use default output handler, false if not
	 */
	public function has_output();
}
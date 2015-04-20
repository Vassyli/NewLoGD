<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

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
	
	/** @var int Allow Anonymous Access */
	const ACCESS_ANONYMOUS        = 0b0001;
	/** @var int Allow Account-Access */
	const ACCESS_ACCOUNT          = 0b0010;
	/** @var int Allow Character-access (if nav allows it) */
	const ACCESS_CHARACTER        = 0b0100;
	/** @var int Allow Character-access even if nav disallow it */
	const ACCESS_CHARACTER_NONNAV = 0b1000;
	
	//public function __construct($model, $row);
	/**
	 * Sets additional arguments to the page.
	 * 
	 * @param array $args sets additional arguments
	 */
	public function set_arguments(array $args);
	/**
	 * Gets additional arguments from the page.
	 * 
	 * return array Additional $_GET arguments
	 */
	public function getArguments();
	/**
	 * Executes all controller code
	 */
	public function execute();	
	/**
	 * Initiate everything
	 */
	public function initiate();	
	/**
	 * Returns a reference to the Model
	 * 
	 * @return \Model Reference to the Model
	 */
	public function getModel();	
	/**
	 * Returns the ID of the page
	 * 
	 * @return string ID of the page
	 */
	public function getId();	
	/**
	 * Returns the type of the page
	 * 
	 * @return string Node of the page
	 */
	public function getType();	
	/**
	 * Returns the title of the page
	 * 
	 * @return string Title of the page
	 */
	public function getTitle();	
	/**
	 * Returns the subtitle of the page
	 * 
	 * @return string Subtitle of the page
	 */
	public function getSubtitle();
	/**
	 * Returns the action of the page
	 * 
	 * @return string action of the page
	 */
	public function getAction();
	/**
	 * Returns the content of the page
	 * 
	 * @return string Content of the page
	 */
	public function getContent();
	/**
	 * Preload the navigation
	 */
	public function loadNavigation();	
	/**
	 * Returns the title of the page
	 * 
	 * @return \Navigation\Container Container containing the navigation
	 */
	public function getNavigation();
	/*
	 * Returns the flag-field
	 * 
	 * @return int All flags
	 */
	public function getFlags();
    /*
	 * Returns the access-field
	 * 
	 * @return int All flags
	 */
	public function getAccess();
	/*
	 * Returns if the page has a certain access flag
	 * 
	 * @param int the Flag to check against.
	 * @return bool true if the requested access flag is set.
	 */
	public function checkAccess($flag);
	
	/**
	 * Checks if the page is editable
	 * 
	 * @return bool true if the page is editable, false if not.
	 */
	public function isEditable();
	/**
	 * Checks if the page is deletable
	 * 
	 * @return bool true if the page is deletable, false if not.
	 */
	public function isDeletable();
	/**
	 * Checks if the page wants to use the parser (Checks the absence of self::FLAG_NO_PARSE)
	 * 
	 * @return bool true if the page is uses the parser, false if not.
	 */
	public function useParser();
	/**
	 * Checks if the page wants to keep it's HTML
	 * 
	 * @return bool true if the page wants to keep the HTML, false if not.
	 */
	public function keepHtml();
	/**
	 * Checks if a page wants to use default output handler
	 * 
	 * @return bool true if a page wants to use default output handler, false if not
	 */
	public function hasOutput();
}
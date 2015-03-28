<?php

// script start time
define("LOGD_SCRIPT_START", microtime(true));

// development config
define("LOGD_SHOW_DEBUG", true);

if(LOGD_SHOW_DEBUG) {
	error_reporting(E_ALL);
}

// some php configuration
define("LOGD_ENCODING", "utf-8");

// get important environment variables
define("LOGD_PATH_ABS", dirname(__FILE__));
define("LOGD_URI_ABS", dirname($_SERVER['PHP_SELF']));

// general configuration
define("LOGD_COPYRIGHT", "Copyright 2002-2003, Game: Eric Stevens; Copyright 2015, Rewrite Game Code: Basilius Sauter");
$a = explode("/", LOGD_URI_ABS);
define("LOGD_SESSIONNAME", "newlogd_".array_pop($a));

// general path configuration
define("LOGD_EXT", ".php");
define("LOGD_LIB_DIRNAME", "lib");
define("LOGD_TEMPLATE_DIRNAME", "template");

define("LOGD_LIB", LOGD_PATH_ABS."/".LOGD_LIB_DIRNAME."/");
define("LOGD_TEMPLATE", LOGD_PATH_ABS."/".LOGD_TEMPLATE_DIRNAME."/");
define("LOGD_TEMPLATE_URI", LOGD_URI_ABS."/".LOGD_TEMPLATE_DIRNAME."/");
define("LOGD_DBCONFIG", LOGD_PATH_ABS . "/dbconfig.php");

// Start output buffering
ob_start();

function filter_nonalpha($string) {
	return preg_replace("/[^[:alpha:]]/ui", '', $string);
}

function debug($string) {
	if(LOGD_SHOW_DEBUG === true) {
		print $string."<br />";
	}
}

// Autoload-Magic
set_include_path(get_include_path() . PATH_SEPARATOR . LOGD_LIB);
spl_autoload_extensions(LOGD_EXT);
spl_autoload_register();
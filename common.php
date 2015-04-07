<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

// script start time
define("LOGD_SCRIPT_START", microtime(true));

// development config
define("LOGD_SHOW_DEBUG", true);
define("LOGD_SHOW_DEBUG_SQL", false);

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

switch(PASSWORD_DEFAULT) {
	case PASSWORD_BCRYPT:
		define("LOGD_PASSWORD_MAXLENGTH", "72"); // In Bytes, not characters!
		break;
}

// general path configuration
define("LOGD_EXT", ".php");
define("LOGD_LIB_DIRNAME", "lib");
define("LOGD_LOCALMODULE_DIRNAME", "localmodule");
define("LOGD_TEMPLATE_DIRNAME", "template");

define("LOGD_LIB", LOGD_PATH_ABS."/".LOGD_LIB_DIRNAME."/");
define("LOGD_LOCALMODULE", LOGD_PATH_ABS."/".LOGD_LOCALMODULE_DIRNAME."/");
define("LOGD_TEMPLATE", LOGD_PATH_ABS."/".LOGD_TEMPLATE_DIRNAME."/");
define("LOGD_TEMPLATE_URI", LOGD_URI_ABS."/".LOGD_TEMPLATE_DIRNAME."/");
define("LOGD_DBCONFIG", LOGD_PATH_ABS . "/dbconfig.php");

// Start output buffering
ob_start();
mb_internal_encoding(LOGD_ENCODING);

function filter_nonalpha($string) {
	return preg_replace("/[^[:alpha:]]/ui", '', $string);
}

function debug($string) {
	if(LOGD_SHOW_DEBUG === true) {
		print "$string\n";
	}
}

function get_gameuri($action) {
	return sprintf("%s/%s", LOGD_URI_ABS, $action);
}

// Autoload-Magic
set_include_path(get_include_path() . PATH_SEPARATOR . LOGD_LIB . PATH_SEPARATOR);
spl_autoload_extensions(LOGD_EXT);
spl_autoload_register();
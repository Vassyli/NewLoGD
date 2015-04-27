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

// some php configuration
define("LOGD_ENCODING", "utf-8");

// get important environment variables
define("LOGD_PATH_ABS", dirname(__FILE__));
define("LOGD_URI_ABS", dirname($_SERVER['PHP_SELF']));

// general configuration
define("LOGD_COPYRIGHT", "Copyright 2015, Game Code: Basilius Sauter\nCopyright 2002-2003, Game: Eric Stevens; ");
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


/**
 * Filters all non-alphabetic characters from a string.
 *
 * @param string $string The string which has to be sanitized
 * @return string The sanitized string
 */
function filter_nonalpha($string) {
	return preg_replace("/[^[:alpha:]]/ui", '', $string);
}

/**
 * Filters all characters that are not: letter, 0-9, _ or -.
 *
 * @param string $string The string which has to be sanitized
 * @return string The sanitized string
 */
function filter_word($string) {
	return preg_replace("/[^\p{L}0-9_-]/u", '', $string);
}

/**
 * Prints information if the constant LOGD_SHOW_DEBUG is set to true
 *
 * @param string $string The debug information
 * @return void
 */
function debug($string) {
	if(LOGD_SHOW_DEBUG === true) {
		print "$string\n";
	}
}

/**
 * Returns the absolute URL of a given $action
 *
 * @param string $action The action for which a url has to be generated
 */
function get_gameuri($action, array $arguments = array()) {
	$uri = sprintf("%s/%s", LOGD_URI_ABS, $action);
    if(!empty($arguments)) {
        foreach($arguments as $arg) {
            $uri .= "/".$arg;
        }
    }
    return $uri;
}

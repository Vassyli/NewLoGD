<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

// Start output buffering
ob_start();

require "common.php";

if(LOGD_SHOW_DEBUG) {
	error_reporting(E_ALL);
}

mb_internal_encoding(LOGD_ENCODING);

// Autoload-Magic
set_include_path(get_include_path() . PATH_SEPARATOR . LOGD_LIB . PATH_SEPARATOR);
spl_autoload_extensions(LOGD_EXT);
spl_autoload_register();

// Prepare Model, Controller and View
$model = new Model($_GET, $_POST);
$controller = new Controller($model);
$view = new View($controller, $model);

// Execute the Controller
$controller->execute();

// View the Result
$view->output();
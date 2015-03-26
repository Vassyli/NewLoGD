<?php
/**
 * @author	Basilius Sauter <basilius.sauter@hispeed.ch>
 *
 * This file is the application's main entry point.
 */

require "common.php";

// Prepare Model, Controller and View
$model = new Model($_GET, $_POST);
$controller = new Controller($model);
$view = new View($controller, $model);

// Execute the Controller
$controller->execute();

// View the Result
$view->output();
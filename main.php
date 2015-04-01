<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
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
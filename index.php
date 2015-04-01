<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

header("Content-type: text/plain; charset=utf-8");

$path = dirname($_SERVER['PHP_SELF']);
$redirect = $path."/main";

header("Location: ".$redirect);
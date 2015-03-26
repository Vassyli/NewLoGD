<?php
/**
 * @author	Basilius Sauter <basilius.sauter@hispeed.ch>
 *
 * This file only redirects to the application entry point (main.php).
 */

header("Content-type: text/plain; charset=utf-8");

$path = dirname($_SERVER['PHP_SELF']);
$redirect = $path."/main";

header("Location: ".$redirect);
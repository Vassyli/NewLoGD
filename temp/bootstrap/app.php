<?php
/**
 *	bootstrap/app.php
 *	
 *	This file contains additional bootstrapping required 
 *	for calls via HTTP.
 *	
 *	Do not modify.
 */

use NewLoGD\Application;

// Load main Bootstrap
require "bootstrap/bootstrap.php";

// Create application
$app = new Application($config, $entityManager);

// Load routes
require "app/Http/routes.php";
// Load middleware
require "app/Http/middleware.php";

// Return $app
return $app;
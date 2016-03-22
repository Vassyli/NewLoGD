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
$extensions->addRoutes($app);

// Load middleware
require "app/Http/middleware.php";
$extensions->addMiddleware($app);

// Return $app
return $app;
<?php
/*
	index.php
	
	This file is the main entry point to the application and should 
	never me modified. Controlling the application should be done 
	through API calls via Routing and Controllers.
*/

// Bootstrapping for the app
$app = require "bootstrap/app.php";

// This runs the application and exits the application.
$app->run();
<?php
/**
 * bootstrap/bootstrap.php
 *	
 * This is a common bootstrap file for different interfaces. It 
 * initializes everything that can be shared between HTTP and CLI calls.
 * @author Basilius Sauter
 * @package
*/

if(php_sapi_name() == "cli") {
    define("CLI", true);
}
else {
    define("CLI", false);
}

// Abort the Script if it defined a non-empty $clionly.
if(!empty($clionly) and CLI === true) {
	die("This file can only be access via cli");
}

use NewLoGD\{Config, Extensions, i18n};

use Doctrine\ORM\{EntityManager, Mapping\AnsiQuoteStrategy, Tools\Setup};

// Useful functions
require_once __DIR__ . "/../src/Helper/functions.php";

// Get Autoloader (cannot use require_once here since it would return "true" if this script is called via doctrine CLI)
$autoloader = require __DIR__ . "/../vendor/autoload.php";

// Get extensions
$extensions = new Extensions(__DIR__ . "/../app/extensions.php");
$extensions->addToAutoloader($autoloader);

// Add Extensions to the auto loader
//$autoloader->addPsr4("\\Extensions\\Commentary\\", "extensions/Commentary");

// Load i18n
$i18n = i18n::getInstance();
$i18n->init();

// Load fundamental application configuration
$config = new Config();
$config->load();

// Take care of database configuration
$annotationSources = ["database"];
$annotationSources = $extensions->addToAnnotationSources($annotationSources);
$db_config = Setup::createAnnotationMetadataConfiguration($annotationSources, $config["db"]["devmode"]);
$db_config->setQuoteStrategy(new AnsiQuoteStrategy());

// Connect to the database
$entityManager = EntityManager::create($config["db"], $db_config);
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

use NewLoGD\Config;
use NewLoGD\i18n;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\AnsiQuoteStrategy;
use Doctrine\ORM\Tools\Setup;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../src/helper/functions.php";

// Load i18n
$i18n = i18n::getInstance();
$i18n->init();

// Load fundamental application configuration
$config = new Config();
$config->load();

// Take care of database configuration
$db_config = Setup::createAnnotationMetadataConfiguration(["database"], $config["db"]["devmode"]);
$db_config->setQuoteStrategy(new AnsiQuoteStrategy());

// Connect to the database
$entityManager = EntityManager::create($config["db"], $db_config);
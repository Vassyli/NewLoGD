<?php
/**
 * database configuration file
 *
 * @package Config
 */

return [
	"devmode" => true,
    
    // MySQL
    
    "dbname" => "newlogd",
    "user" => "root",
    "password" => "hsenheff",
    "host" => "localhost",
    "driver" => "pdo_mysql",
	
    // SQlite 
    /*
    "driver" => "pdo_sqlite",
	"path" => "pdo.sqlite",*/
];
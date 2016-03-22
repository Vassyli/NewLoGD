<?php
/**
 * Meta-Informations about the Extension
 */

use const NewLoGD\Application\{GET, POST, PUT,DELETE};

return [
    "name" => "Commentary",
    "version" => "0.1-dev",
    "author" => "Basilius Sauter",
    "routes" => [
        [GET, "", "#CommentaryController@all"],
    ],
];
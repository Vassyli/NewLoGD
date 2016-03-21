<?php
/**
 * app/http/middleware.php
 * 
 * Use this file to add Middleware. A Middleware is a class that has at least 
 * one out of 4 hooks to execute code either before the Controller is called or
 * at the end.
 * 
 * [API]
 * $app->addMiddleware($class)
 * 
 * $class:
 *      The name of the class
 */

$app->addMiddleware(\App\Http\Middleware\SessionMiddleware::class);
$app->addMiddleware(\App\Http\Middleware\AuthMiddleware::class);
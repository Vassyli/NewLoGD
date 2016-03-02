<?php

$app->addMiddleware(\App\Http\Middleware\SessionMiddleware::class);
$app->addMiddleware(\App\Http\Middleware\AuthMiddleware::class);
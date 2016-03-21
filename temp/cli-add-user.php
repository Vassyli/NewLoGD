<?php

$clionly = true;

require "bootstrap/bootstrap.php";

use Database\User;

$name = $argv[1];
$pass = $argv[2];

var_dump($name, $pass);

$user = new User();
$user->setName($name);
$user->setPassword($pass);

$entityManager->persist($user);
$entityManager->flush();

echo "Created User with ID ".$user->getId()."\n";
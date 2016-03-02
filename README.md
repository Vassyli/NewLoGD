General
=======

NewLoGD is browsergame engine written in PHP. It is going to be a rewrite of LoGD 0.9.7+jt ext GER using modern programming patterns and strong frameworks to ease the creation of Plugins.

## Requirements

For running this game, you'll need:
- A webserver with support for PHP
- PHP 7.0.0 or higher
- A SQL backend (in theory any backend supported by Doctrine)

## Installation

1. Download the files
2. Go to the ./config folder and rename all *.php.dist files to *.php
3. Open ./config/db.php and add your database credentials
4. Open ./config/auth.php and add the app-id and app-secret for facebook or google (or both!) and change the key "enabled" to true.
5. Run the following commands:
```
$ composer install
$ vendor/bin/doctrine orm:schema-tool:create
```
6. Open a browser and call index.html to try everything out.

Aim
===

The aim of this project (for version 1.0) is to have feature-parity with LoGD 0.9.7+jt ext GER by combining a headless API with a HTML/JS client to talk to that API.

Design Philosophy
=================

NewLoGD implements a MVC-like pattern: A controller connected to a route executes Code and delivers an answer either as plain text or as a JSON encoded array ready to be used by Javascript. Models are providing abstraction from the database and are built on top of Doctrine's ORM.

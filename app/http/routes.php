<?php
/**
 * app/http/routes.php
 *	
 *	Use this file to add routes. A route is a URL path that defined what 
 *	part of this application has to work.
 *	It can be done by simply connecting a static or a dynamic to a closure 
 *	or a controller.
 *	
 *	[API]
 *	$app->addRoute($requesttype
 *	
 *	[Variables]
 *	$requesttype:
 *		This arguments defines the type of request the route is listening to:
 *			- A\GET
 *			- A\POST
 *			- A\PUT
 *			- A\DELETE
 *	
 *	$route:
 *		A route always starts with a leading slash (/). The root route (/) 
 *		should always return some basic informations, such as API version, 
 *		contact informations and the like.
 *		Routes must not have a trailing slash (/).
 *		
 *		A static route is a simple string that matches exactly the URL.
 *		E.g, "example.com/test" matches the route "/test" and nothing else.
 *		
 *		A dynamic route can match a whole subclass of URLs and can be used for 
 *		pagination, getting entries by their ID or by another key.
 *		E.g., the route "/user/{int}" matches "/user/1", "/user/200", but not "/user/test" or "/user/b17".
 *		Placeholders do not have to be separated by slashes, they can also be used 
 *		different combinations like "/user_{int}" or "/test/{id}_{alpha}".
 *		Useable Placeholders are:
 *			- {id}			Any integer >= 0
 *			- {int}			Any integer
 *			- {word}		Any word
 *			- {text}		Any text 
 *		
 *		You must never use any special characters except underscore (_) and dash (-).
 *		
 *	$controller:
 *		The controller gets called when $requesttype and $route are matching the request.
 *		Upon calling, the $controller has to return a valid response (see app/http/controller/controller.php)
 *		for more informations.
 *		
 *		If you'd like to call a method of a custom Controller class you have to use a string 
 *		containing the name of the Controller and the Method separated by @.
 *		E.g., "Controller@test" creates an instance of the class Controller and calls it's method test().
 *		
 *	[Examples]
 *		// This adds the root route that is listening to a GET request and returns an array that gets
 *		// json-encoded by the default Controller.
 *		$app->addRoute(A\GET, "/", 
 *			function($app, $response) {
 *				return [
 *					"version" => "0",
 *					"comment" => "Hallo Welt"
 *				];
 *			}
 *		);
 *
 * @package App
 */

// Do not delete the use statements line
use NewLoGD\Application as A;
use NewLoGD\Session;

// Modifications are ok after this line

/*$app->addRoute(A\GET, "/", function($app, $response) { 
return [
	"version" => "0.1-dev",
	"comment" => "NewLoGD",
	"hits" => Session::get("hits"),
]; });*/
$app->addRoute(A\GET, "/", "MainController@run");
$app->addRoute(A\GET, "/logout", function() {
    Session::drop();
    return "Session dropped";
});

$app->addRouteGroup("/user", [
    [A\GET, "", "UserController@all"],
    [A\GET, "/{id}", "UserController@getUser"],
]);

$app->addRouteGroup("/character", [
    [A\GET, "", "CharacterController@all"],
    [A\GET, "/current", "CharacterController@getCurrentCharacter"],
    [A\PUT, "/current/{id}", "CharacterController@setCurrentCharacter"],
    [A\GET | A\POST, "/create", "CharacterController@creation"],
    [A\GET, "/{id}", "CharacterController@getCharacter"],
]);

$app->addRouteGroup("/auth", [
	[A\GET, "", "AuthController@all"],
	[A\POST, "", "AuthController@token"],
	[A\GET, "/{word}", "AuthController@auth"],
]);

$app->addRouteGroup("/scene", [
   [A\GET, "", "SceneController@getScene"], 
]);

$app->addRoute(A\GET, "/test", "Controller@test");
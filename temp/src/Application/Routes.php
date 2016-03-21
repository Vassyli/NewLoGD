<?php
/**
 * src/application/routes.php - Trait for route managing
 * 
 * @author Basilius Sauter
 * @package NewLoGD
 * @subpackage Application
 */

namespace NewLoGD\Application;

const GET = 0b0001;
const POST = 0b0010;
const PUT = 0b0100;
const DELETE = 0b1000;

const DYNAMIC_PLACEHOLDERS = [
	"{id}" => "([0-9]+)",
	"{word}" => "([a-zA-Z]+)"
];

/**
 * Route trait
 *
 * This trait has methods to add and resolve routes from an object
 * @package NewLoGD
 * @subpackage Application
 */
trait Routes {
	/** @var array array of static routes $static_routes["/"] = ["request_method" => $method, "call" => $function] */
	public $static_routes = [];
	/** @var array array of dynamic routes, nearly identical to static routes */
	public $dynamic_routes = [];
    
    /** @var string $path The path */
    public $path = "/";
	
	/**
	 * Connects a request method and a route to a callback function
	 *
	 * @param int $method A request method identifier: GET, POST, PUT or DELETE
	 * @param string $route The route that has to be listened to
	 * @param mixed $function Either a closure or a string containts class name and method name separated by @, eg Controller@test
	 */
	public function addRoute(int $method, string $route, $function) {
		// Decide whether route is dynamic or static
		//   Crude detection for now: The presence of at least one { defines a dynamic route.
		if (strpos($route, "{") === false) {
			if(!isset($this->static_routes[$route])) {
				$this->static_routes[$route] = [];
			}
			
			$this->static_routes[$route][] = ["request_method" => $method, "call" => $function];
		}
		else {
			if(!isset($this->dynamic_routes[$route])) {
				$this->dynamic_routes[$route] = [];
			}
			
			$this->dynamic_routes[$route][] = ["request_method" => $method, "call" => $function];
		}
	}
	
	/**
	 * Connects a series of request methods and routes to controllers with a common prefix
	 * 
	 * @param string $prefix The prefix common to all routes
	 * @param array $routes A list of routes without prefix (same arguments as in $this->addRoute())
	 */
	public function addRouteGroup(string $prefix, array $routes) {
		foreach($routes as $route) {
			$finalroute = $prefix.$route[1];
			
			$this->addRoute($route[0], $finalroute, $route[2]);
		}
	}
	
	/**
	 * Normalizes the requested path
	 *
	 * This method tries to normalize the requested path by 
	 * changing an empty one to "/" and removing a trailing
	 * slash if there is one.
	 */
	protected function preparePath() : string {
		if(empty($_GET["path"])) {
			$path = "/";
		}
		else {
			$path = $_GET["path"];
			
			// Remove trailing slash (/)
			if($path[\strlen($path)-1] == "/") {
				$path = \substr($path, 0, -1);
			}
		}
		
		return $path;
	}
	
	/**
	 * Resolves the route
     * @return array [0] => normalized path, [1] found registered for the path controller
	 */
	protected function resolveRoute() : array {
		// Initialize Variables
		$path = $this->preparePath();
        $this->path = $path;
		
		$arguments = [];
		
		// Try to resolve for static route
		$controller = $this->resolveStaticRoute($path);
		
		if($controller === false){
			$controller = $this->resolveDynamicRoute($path, $arguments);
		}
		
		return [$path, $controller, $arguments];
	}
    
    /**
     * Returns the called path
     * @return string The path
     */
    public function getPath() {
        return $this->path;
    }
	
	/**
	 * Tries to resolve the route as a static route
	 * @param string $path The (normalized) path
	 * @return bool|array Returns the route if found or false if no route has been found
	 */
	protected function resolveStaticRoute(string $path) {
		$found_route = false;
		
		foreach($this->static_routes as $route => $routes) {
			// Continue if this is not the searched path
			if($path != $route) {
				continue;
			}
			
			// Search for the correct request mode
			foreach($routes as $subroute) {
				if($subroute["request_method"] & $this->request_method) {
					$found_route = $subroute;
					break;
				}
			}
		}
		
		return $found_route;
	}
	
	/**
	 * Tries to resolve the route as a dynamic route
	 * @param string $path The (normalized) path
	 * @param array &$arguments  A reference to an array used to store URL arguments
	 * @return bool|array Returns the found route or false if none has been found
	 */
	protected function resolveDynamicRoute(string $path, array &$arguments) {
		$found_route = false;
		
		// Prepare Translation for preg_replace/preg_match
		$replacements = DYNAMIC_PLACEHOLDERS;
		
		foreach($this->dynamic_routes as $route => $routes) {
			// Prepare search pattern
			$route = str_replace(array_keys($replacements), array_values($replacements), $route);
			$pattern = "#^".$route."$#";
			
			// Continue if this is not the searched path
			if(preg_match($pattern, $path) === 0) {
				continue;
			}
			
			// Search for the correct request mode
			foreach($routes as $subroute) {
				if($subroute["request_method"] & $this->request_method) {
					// Route gefunden
					$found_route = $subroute;
					// Collect arguments
					$args = [];
					preg_match_all($pattern, $path, $args);
					$total = count($args);
					for($i=1;$i<$total;$i++) {
						$arguments[] = $args[$i][0];
					}
					
					break;
				}
			}
		}
		
		return $found_route;
	}
}
<?php
/**
 * Definition of main application class
 * 
 * @package NewLoGD
 * @author Basilius Sauter
 */
declare(strict_types=1);

namespace NewLoGD;

use NewLoGD\Application as App;
use NewLoGD\Application\Routes;
use NewLoGD\Application\Middleware;
use NewLoGD\Auth;
use NewLoGD\Config;
use NewLoGD\HttpResponse;
use NewLoGD\Session;
use App\Http\Controllers;
use App\Models;
use App\Models\UserModel;
use Doctrine\ORM\EntityManager;
use Hybridauth\Hybrid_Auth;

/**
 * Main class controlling the http application
 *
 * This class manages the whole http application. It does advanced bootstrapping
 * such as preparing request variables and is a defined interface for accessing 
 * important interfaces (such as database or configuration).
 * It also connects routes to controllers.
 *
 */
class Application {
	use Routes;
    use Middleware;
	
	/** @var int The request method (App\GET, App\POST, App\PUT or App\DELETE) */
	protected $request_method = 0;
	
	/** @var Config Reference to the configuration class */
	protected static $config = NULL;
	/** @var EntityManager Reference to the Doctrine entity manager */
	protected static $entityManager = NULL;
	/** @var Auth Reference to Auth */
	protected static $auth = NULL;
	
	/**
	 * Constructor
	 * 
	 * @param Config $config A instance of config
	 * @param EntityManager $entityManager A instance of the Doctrine entity manager.
	 */
	public function __construct(Config $config, EntityManager $entityManager) {
		// Save interface references in static
		self::$config = $config;
		self::$entityManager = $entityManager;
		
		// Get request method
		switch($_SERVER["REQUEST_METHOD"]) {
			case "GET": $this->request_method = App\GET; break;
			case "POST": $this->request_method = App\POST; break;
			case "PUT": $this->request_method = App\PUT; break;
			case "DELETE": $this->request_method = App\DELETE; break;
		}
	}
    
    public function getRequestMethod() {
        return $this->request_method;
    }
	
	/**
	 * Returns a reference to the configuratio manager or the requested variable
	 * @param string $configtype type of config to return
	 * @param string $configkey key of config to return
	 * @return mixed Config if no parameters where given, or else mixed depending on the config requested
	 */
	public static function getConfig(string $configtype = NULL, string $configkey = NULL) { 
		if($configtype === NULL)
			return self::$config;
		elseif($configkey === NULL)
			return self::$config[$configtype]??NULL;
		else
			return self::$config[$configtype][$configkey]??NULL;
	}
	/**
	 * Returns a reference to the Doctrine entity manager
	 */
	public static function getEntityManager() : EntityManager { return self::$entityManager; }
	/**
	 * Returns a reference to Auth
	 */
	public static function getAuth() : Auth { return self::$auth; }
    /** Sets a reference to Auth */
    public static function setAuth(Auth $auth) { self::$auth = $auth; }
	
	/**
	 * Calls the appropriate controller for the requested path
	 * @param HttpResponse $response Http Response obect
	 * @param array $found_route found route
	 * @param array $arguments arguments found in url
	 */
	protected function callController(HttpResponse $response, $found_route, array $arguments) {
		if($found_route) {
			if(is_callable($found_route["call"])) {
				// Start default Controller
				$controller = new Controllers\Controller($this, $response);
				$controller->call($found_route["call"], $arguments);
			}
			elseif(is_string($found_route["call"])) {
				$call = explode("@", $found_route["call"]);
                $controllerclass = "\\App\\Http\\Controllers\\".$call[0];
				$controller = new $controllerclass($this, $response);
                
                if($controller->checkAccess()) {
                    $controller->call($call[1], $arguments);
                }
                else {
                    $response->forbidden("You are not authorized to access this route.");
                }
				//$return = $controller->{$call[1]}();
			}
		}
		else {
			// No route found - return 404
			$response->notFound("Path not found.");
		}
	}
	
	/**
	 * Runs the application (initialize session, routing, call controller, shut everything down)
	 */
	public function run() {		        
        list($path, $controller, $arguments) = $this->resolveRoute();
		
		// Create HttpResponse Object
		$response = new HttpResponse($path);
        
        $this->runMiddleware(Application\MIDDLEWARE_HEAD, $response);
        $this->runMiddleware(Application\MIDDLEWARE_BOTTOM, $response);
		
		// Call controller if middleware has not finalized request
        if($response->isFinalized() === false) {
            $this->callController($response, $controller, $arguments);
        }
		
		// Sends the HttpResponse object to the client
		$response->send();
        
        //$this->runMiddleware("terminate");
        
        // Save changes to database
        if(Auth::getActiveUser() !== NULL) {
            self::getEntityManager()->persist(Auth::getActiveUser());
        }
        
        self::getEntityManager()->flush();
	}
}
<?php
/**
 * app/http/middleware/SessionMiddleware.php
 * 
 * @author Basilius Sauter
 * @package App
 * @subpackage Http\Middleware
 */

namespace App\Http\Middleware;

use NewLoGD\Interfaces\{MiddlewareHead, MiddlewareTerminateBottom};
use NewLoGD\Application;
use NewLoGD\HttpResponse;
use NewLoGD\Session;

/**
 * Session middleware loads and closes the session
 */
class SessionMiddleware implements MiddlewareHead, MiddlewareTerminateBottom {
    /**
     * {@inherit}
     * @param HttpResponse $httpresponse The HttpResponse object
     * @return bool True if it is okay to call the next middleware
     */
    public function head(HttpResponse $httpresponse) : bool {
        // Initialize Session
		Session::initialize(Application::getConfig());
		
		// Hit-Statistics
		Session::put("hits", (Session::get("hits") === NULL ? 1 : Session::get("hits")+1));        
        return true;
    }
    
    /**
     * {@inherit}
     * @param HttpResponse $httpresponse The HttpResponse object
     * @return bool True if is okay to call the next middleware
     */
    public function terminateBottom(HttpResponse $httpresponse) : bool {
        return true;
    }
}

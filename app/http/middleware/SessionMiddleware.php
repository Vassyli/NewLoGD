<?php
/**
 * @author Basilius Sauter
 * @package App
 * @subpackage Http\Middleware
 */

namespace App\Http\Middleware;

use NewLoGD\Interfaces\MiddlewareHead;
use NewLoGD\Interfaces\MiddlewareTerminateBottom;
use NewLoGD\Application;
use NewLoGD\HttpResponse;
use NewLoGD\Session;

/**
 * Session middleware loads and closes the session
 */
class SessionMiddleware implements MiddlewareHead, MiddlewareTerminateBottom {
    public function head(HttpResponse $httpresponse) : bool {
        // Initialize Session
		Session::initialize(Application::getConfig());
		
		// Hit-Statistics
		Session::put("hits", (Session::get("hits") === NULL ? 1 : Session::get("hits")+1));        
        return true;
    }
    
    public function terminateBottom(HttpResponse $httpresponse) : bool {
        return true;
    }
}

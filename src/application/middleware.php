<?php
/**
 * @author Basilius Sauter
 * @package NewLoGD
 * @subpackage Application
 */

namespace NewLoGD\Application;

const MIDDLEWARE_HEAD = 1;
const MIDDLEWARE_BOTTOM = 2;
const MIDDLEWARE_TERMINATE_HEAD = 4;
const MIDDLEWARE_TERMINATE_BOTTOM = 8;

const MIDDLEWARE = [
    MIDDLEWARE_HEAD => "runMiddlewareHead",
    MIDDLEWARE_BOTTOM => "runMiddlewareBottom",
    MIDDLEWARE_TERMINATE_HEAD => "runMiddlewareTerminateHead",
    MIDDLEWARE_TERMINATE_BOTTOM => "runMiddlewareTerminateBottom",
];

use NewLoGD\Interfaces\MiddlewareHead;
use NewLoGD\Interfaces\MiddlewareBottom;
use NewLoGD\Interfaces\MiddlewareTerminateHead;
use NewLoGD\Interfaces\MiddlewareTerminateBottom;
use NewLoGD\HttpResponse;

/**
 * Description of middleware
 */
trait middleware {
    /** @var array $midleware Stack of general middleware objects */
    protected $middleware = [];
    /** @var array $path_middleware Stack of path-specific middleware objects */
    protected $path_middleware = [];
    /** @var int|null $middleware_stop Position at which the stack stopped during normal run */
    protected $middleware_stop = null;
    /** @var int|null $middleware_terminate_stop Position at which the stack stopped during termination */
    protected $middleware_terminate_stop = null;
    
    /**
     * Instantiates a class and adds it to the middleware stack
     * @param string $middleware
     */
    public function addMiddleware(string $middleware) {
        $this->middleware[] = new $middleware();
    }
    
    /*
     * Runs middleware
     * @param int $where Which Middleware method to run (MIDDLEWARE_HEAD, _BOTTOM, _TERMIANTE_HEAD or _TERMINATE_BOTTOM)
     * @param HttpResponse $httpresponse The HTTP Response object
     */
    public function runMiddleware(int $where, HttpResponse $httpresponse) {
        if(isset(MIDDLEWARE[$where])) {
            $method = MIDDLEWARE[$where];
            $this->$method($httpresponse);
        }
    }
    
    /**
     * Runs through all registered middleware objects that have implemented 
     * MiddlewareHead and stops as soon as one of them returns false.
     * @param HttpResponse $httpresponse The HTTP Response object
     */
    protected function runMiddlewareHead(HttpResponse $httpresponse) {
        $i = 0;
        foreach($this->middleware as $middleware) {
            if($middleware instanceof MiddlewareHead) {
                $continue = $middleware->head($httpresponse);
            }
            else {
                $continue = true;
            }
            
            $i++;
            
            if($continue == false) {
                break;
            }
        }
        $this->middleware_stop = ($i - 1);
    }
    
    /**
     * Runs through all registered middleware objects that have implemented 
     * MiddlewareBottom, starting at the position where runMiddlewareHead 
     * stopped, until it the middleware returns false.
     * @param HttpResponse $httpresponse The HTTP Response object
     */
    protected function runMiddlewareBottom(HttpResponse $httpresponse) {
        for($i = $this->middleware_stop; $i >= 0; $i--) {
            $middleware = $this->middleware[$i];
            
            if($middleware instanceof MiddlewareBottom) {
                $continue = $middleware->bottom($httpresponse);
            }
            else {
                $continue = true;
            }
            
            if($continue == false) {
                break;
            }
        }
    }
    
    /**
     * Runs through all registered middleware objects that have implemented 
     * MiddlewareTerminateHead and stops as soon as one of them returns false.
     * @param HttpResponse $httpresponse The HTTP Response object
     */
    protected function runMiddlewareTerminateHead(HttpResponse $httpresponse) {
        $i = 0;
        foreach($this->middleware as $middleware) {
            if($middleware instanceof MiddlewareTerminateHead) {
                $continue = $middleware->terminateHead($httpresponse);
            }
            else {
                $continue = true;
            }
            
            $i++;
            
            if($continue == false) {
                break;
            }
        }
        $this->middleware_terminate_stop = ($i - 1);
    }
    
    /**
     * Runs through all registered middleware objects that have implemented 
     * MiddlewareTerminateBottom, starting at the position where runMiddlewareTerminateHead 
     * stopped, until it the middleware returns false.
     * @param HttpResponse $httpresponse The HTTP Response object
     */
    protected function runMiddlewareTerminateBottom(HttpResponse $httpresponse) {
        for($i = $this->middleware_terminate_stop; $i >= 0; $i--) {
            $middleware = $this->middleware[$i];
            
            if($middleware instanceof MiddlewareTerminateBottom) {
                $continue = $middleware->terminateBottom($httpresponse);
            }
            else {
                $continue = true;
            }
            
            if($continue == false) {
                break;
            }
        }
    }
}

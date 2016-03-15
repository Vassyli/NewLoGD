<?php
/**
 * src/interfaces/Middleware.php
 * @author Basilius Sauter
 * @pacakge NewLoGD
 * @subpackage Interfaces
 */

namespace NewLoGD\Interfaces;

use NewLoGD\HttpResponse;

/**
 * Interface for middleware that gets executed at the "bottom"
 * @author Basilius Sauter
 */
interface MiddlewareBottom { 
    /**
     * Hook for the interface
     * @param HttpResponse $httpresponse
     */
    public function bottom(HttpResponse $httpresponse) : bool;
}

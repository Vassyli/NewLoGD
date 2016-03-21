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
 * Interface for middleware that gets executed at the "head" during page termination
 * @author Basilius Sauter
 */
interface MiddlewareTerminateHead {
    /**
     * Hook for the interface
     * @param HttpResponse $httpresponse
     */
    public function terminateHead(HttpResponse $httpresponse) : bool;
}

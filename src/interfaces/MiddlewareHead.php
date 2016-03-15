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
 * Interface for middleware that gets executed at the "head"
 * @author Basilius Sauter
 */
interface MiddlewareHead {
    /**
     * Hook for the interface
     * @param HttpResponse $httpresponse
     */
    public function head(HttpResponse $httpresponse) : bool;
}

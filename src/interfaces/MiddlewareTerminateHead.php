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
 *
 * @author Basilius Sauter
 */
interface MiddlewareTerminateHead {
    public function terminateHead(HttpResponse $httpresponse) : bool;
}

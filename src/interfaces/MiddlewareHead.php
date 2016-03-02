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
interface MiddlewareHead {
    public function head(HttpResponse $httpresponse) : bool;
}

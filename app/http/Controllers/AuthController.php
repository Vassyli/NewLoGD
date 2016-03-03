<?php
/**
 * app/http/controllers/AuthController.php
 *
 * Controller for user authentication
 * @author Basilius Sauter
 * @package App
 * @subpackage Http\Controllers
 */

namespace App\Http\Controllers;

/**
 * Controller for user authentication
 */
class AuthController extends Controller {
	/**
	 * Lists all registered auth methods
	 */
	public function all() {
		$authconf = $this->app->getConfig()["auth"];
		$realauths = [];
		foreach($authconf as $key => $auth) {
			if(isset(\NewLoGD\PROVIDER[$key]) and !empty($auth["enabled"])) {
				$realauths[$key] = [
                    "name" => $auth["text"]["name"],
                    "logintext" => $auth["text"]["logintext"]
                ];
			}
		}
		return $realauths;
	}
	
	/**
	 * Returns details about how to authorize to a given provider
	 * @param string $provider
	 */
	public function auth($provider) {
		$auth = $this->app->getAuth();
		
		return $auth->getAuthDetails($provider);
	}
	
	public function token() {
		$token = $_POST["access_token"];
		
		$answer = $this->app->getAuth()->validateToken($token);
		return $answer;
	}
}

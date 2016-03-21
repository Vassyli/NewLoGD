<?php
/**
 * app/http/controllers/MainController.php
 *
 * Controller for main app calls
 * @author Basilius Sauter
 * @package App
 * @subpackage Http\Controllers
 */

namespace App\Http\Controllers;

use NewLoGD\Application;
use NewLoGD\Auth;
use NewLoGD\Session;

/**
 * Controller for main app calls
 */
final class MainController extends Controller {
    /**
     * Returns general informations about the Game Server and User Login state
     * @return type
     */
	public function run() {
		$return = [
			"version" => Application::getConfig("app", "version"),
			"gametitle" => Application::getConfig("app", "gametitle"),
			"pagehits" => Session::get("hits"),
			"auth.profile" => Session::get("auth.profile"),
			"auth.provider" => Session::get("auth.provider"),
            "userid" => Session::get("userid"),
            "activeuser" => Auth::getActiveUser(),
            "loginstate" => Auth::getLoginState(),
		];
		
		return $return;
	}
}

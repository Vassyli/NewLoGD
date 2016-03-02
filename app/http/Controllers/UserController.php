<?php
/**
 * app/http/controllers/UserController.php
 *
 * Controller for user management
 */

namespace App\Http\Controllers;

use App\Models\UserModel as User;

/**
 * Controller for user management
 */
class UserController extends Controller {
	/**
	 * Returns all Users
	 * @return array Array of Users
	 */
	public function all() : array {
		return User::all();
	}
	
	/**
	 * Gets a user by a specific ID
	 * @param int $id user id
	 * @return array The User or a HttpResponse StatusCode-Array
	 */
	public function getUser(int $id) {
		$found = User::find($id);
		if($found === null) {
			return [404, "No user with the requested ID has been found"];
		}
		else {
			return $found;
		}
	}
}

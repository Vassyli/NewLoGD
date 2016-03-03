<?php
/**
 * app/http/controllers/CharacterController.php
 *
 * @author Basilius Sauter
 * @package App
 * @subpackage Http/Controllers
 */

namespace App\Http\Controllers;

use NewLoGD\Auth;
use App\Models\CharacterModel as Character;
use App\Models\UserModel as User;

/**
 * Controller for character management
 */
class CharacterController extends Controller {
    protected $allow_anonymous = false;
            
	/**
	 * Returns all characters owned by current user
	 * @return array Array of character properties
	 */
	public function all() {
        $characters = Auth::getActiveUser()->getCharacters();
        $return = [];
        
        foreach($characters as $character) {
            $return[] = Character::getPublicFields($character);
        }
        
        return $return;
	}
	
	public function getCharacter(int $id) {
        $character = Character::_find($id);
        
        if($character === null) {
            $this->response->notFound("No character with this ID exists");
            return false;
        }
        elseif($character->getOwner() == Auth::getActiveUser()) {
            return Character::getPublicFields($character);
        }
        else {
            $this->response->forbidden("This character is not yours.");
            return false;
        }
	}
}

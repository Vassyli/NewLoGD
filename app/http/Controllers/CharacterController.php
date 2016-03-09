<?php
/**
 * app/http/controllers/CharacterController.php
 *
 * @author Basilius Sauter
 * @package App
 * @subpackage Http/Controllers
 */

namespace App\Http\Controllers;

use NewLoGD\Application as App;
use NewLoGD\Auth;
use NewLoGD\Form;
use NewLoGD\i18n;

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
    
    public function getCreateForm() {
        $form = new Form($this->app->getPath());
        $form->title(i18n::_("formtitle", "character"));
        $form->varchar("name", i18n::_("name", "character"), [
                    "required" => true, 
                    "validate" => [
                        "minlength" => 2,
                        "maxlength" => 50,
                    ]
                ]);
        
        if($this->app->getRequestMethod() == App\POST) {
            $form->setResults($_POST)->validate();
            
            if($form->isValid()) {
                $results = $form->getResults();
                
                if(count(Character::_findByName($results["name"])) > 0) {
                    $this->response->invalidData(i18n::_("creation_nameinuse", "character"));
                }
                else {
                    // Create character
                    $character = Character::_create($results["name"]);
                    // Set character owner to current user
                    Auth::getActiveUser()->addCharacter($character);
                    
                    App::getEntityManager()->persist($character);
                    App::getEntityManager()->flush();
                    
                    return [i18n::_("creation_success", "character"), $character->getId()];
                }
            }
            else {
                $this->response->invalidData($form->getValidationErrors());
            }
        }
        else {
            return $form;
        }
    }
}

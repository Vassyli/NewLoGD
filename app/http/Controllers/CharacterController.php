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
	
    /**
     * Returns informations about a specific character.
     * @param int $id Primary id of the character
     * @return array Information about the specific character
     */
	public function getCharacter(int $id) : array {
        $character = Character::_find($id);
        
        if($character === null) {
            $this->response->notFound("No character with this ID exists");
        }
        elseif($character->getOwner() != Auth::getActiveUser()) {
            $this->response->forbidden("This character is not yours.");
        }
        else {
            return Character::getPublicFields($character);
        }        
        
        return [];
	}
    
    /**
     * Returns information about the current active character.
     * @return array Information about the current character
     */
    public function getCurrentCharacter() : array {
        $character = Auth::getActiveUser()->getCurrentCharacter();
        
        if($character === NULL) {
            $this->resonse->notFound(i18n::_("nucurrent", "character"));
            return [];
        }
        
        return Character::getPublicFields($character);
    }
    
    /**
     * Tries to set the current character to a given ID
     * @param int $id The primary id of the character
     * @return bool True if successful, False of not.
     */
    public function setCurrentCharacter(int $id) : bool {
        // Get requested character
        $character = Character::_find($id);
        
        // Check ownership
        if(Auth::getActiveUser() != $character->getOwner()) {
            // active User does not own this character.
            $this->response->forbidden(i18n::_("notowner", "character"));
            return false;
        }
        
        try {
            Auth::getActiveUser()->setCurrentCharacter($character);
            $this->response->noContent();
            return true;
        }
        catch(\PDOException $e) {
            $this->response->forbidden(i18n::_("inuse", "character"));
            return false;
        }
    }
    
    /**
     * Returns an array describing the form needed to create a character - and, 
     * if this method is called via a POST, it returns error information about 
     * the form and, if successfull, it creates the character.
     * @return array GET: Array describing the formular; POST: Array describing errors or a success message
     */
    public function creation() : array {
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
            return $form->jsonSerialize();
        }
    }
}

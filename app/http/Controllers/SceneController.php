<?php
/*
 * app/http/controller/SceneController.php
 * 
 * @author Basilius Sauter
 * @package App
 * @subpackage Http\Controllers
 */

namespace App\Http\Controllers;

use Database\{Character, CharacterScene, Scene, SceneAction};

use NewLoGD\Application as App;
use NewLoGD\Auth;
use NewLoGD\Exceptions\{RequestForbidden, RequestNotFound, InvalidData, InternalError};
use NewLoGD\i18n;

/**
 * Controls everything related to a Scene
 */
class SceneController extends Controller {
    /** {@inherit} */
    protected $allow_anonymous = false;
    
    protected function getCharacter() : Character {
        $character = Auth::getActiveUser()->getCurrentCharacter();
        
        if($character === NULL) {
            throw new RequestForbidden();
        }
        
        return $character;
    }
    
    protected function getDefaultScene(Character $character) {
        $default = App::getEntityManager()->find(App::table("Scene"), 1);

        if($default === null) {
            return [
                "title" => i18n::_("badnav_title", "scene"),
                "body" => i18n::_("badnav_body", "scene"),
            ];
        }
        else {/*
            $charscene = App::table("CharacterScene");
            $defaultscene = new $charscene();
            $defaultscene->fillFromScene($default);
            $defaultscene->setCharacter($character);
            $character->setScene($defaultscene);*/
            $character->switchScene($default);
        }

        $scene = $character->getScene();
            
        return $character->getScene();
    }
    
    /**
     * Returns the scene of the current user.
     * 
     * This method fetches the active user and his current selected character.
     * It answers the request with an error 403 if the user has not yet selected 
     * an active character. Else, it returns an array describing the current scene.
     * @return bool|array False if error 403 or else an array describing the scene.
     */
    public function getScene() {
        $character = $this->getCharacter();
        $scene = $character->getScene();

        if($scene === NULL) {
            // The Scene is empty. This is usually in order to fix "badnavs" or
            // if the character never has been used before. Thus, we proceed to 
            // fetch the default scene.
            // @ToDo: Store the ID of the default Scene in the database.
            $scene = $this->getDefaultScene($character);
        }
        
        return is_array($scene) ? $scene : [
                "title" =>  $scene->getTitle(),
                "body" => $scene->getBody(),
                "actions" => $scene->getActions(),
            ];
    }
    
    public function switchScene() {
        if(!isset($_POST["id"])) {
            throw new InvalidData("[SceneController] Malformed Request");
        }
        
        $id = $_POST["id"];
        $character = $this->getCharacter();
        $scene = $character->getScene();
        
        if($scene === NULL) {
            throw new RequestForbidden("[SceneController] Cannot change Scene without a Scene");
        }
        
        $actions = $scene->getActions();
        $found_target = NULL;
        foreach($actions as $action) {
            if($action["id"] == $id) {
                $found_target = $action;
                break;
            }
            
            foreach($action["childs"] as $childaction) {
                if($childaction["id"] == $id) {
                    $found_target = $childaction;
                    break 2;
                }
            }
        }
        
        if($found_target === NULL) {
            throw new RequestNotFound("[SceneController] The selected action cannot be found.");
        }
        
        if(is_numeric($id)) {
            // Numeric ActionID means Action comes from the database.
            $action = SceneAction::find($id);
            if($action === NULL) {
                throw new InternalError("[SceneController] Cannot find the requested Scene.");
            }
            
            $new_scene = $action->getTargetScene();
            if($scene === NULL) {
                throw new InternalError("[SceneController] The Scene connected to this action us unknown.");
            }
            
            $character->switchScene($new_scene);
            return "OK";
        }
        else {
            // Non-numeric - custom action, probably due to modules?
            // @ToDo: Implement!
        }
    }
}
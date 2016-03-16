<?php
/*
 * app/http/controller/SceneController.php
 * 
 * @author Basilius Sauter
 * @package App
 * @subpackage Http\Controllers
 */

namespace App\Http\Controllers;

use NewLoGD\Application as App;
use NewLoGD\Auth;
use NewLoGD\i18n;

/**
 * Controls everything related to a Scene
 */
class SceneController extends Controller {
    /** {@inherit} */
    protected $allow_anonymous = false;
    
    /**
     * Returns the scene of the current user.
     * 
     * This method fetches the active user and his current selected character.
     * It answers the request with an error 403 if the user has not yet selected 
     * an active character. Else, it returns an array describing the current scene.
     * @return bool|array False if error 403 or else an array describing the scene.
     */
    public function getScene() {
        $character = Auth::getActiveUser()->getCurrentCharacter();
        
        if($character === NULL) {
            $this->response->forbidden("You need an active User for this.");
            return false;
        }
        else {
            $scene = $character->getScene();
            
            if($scene === NULL) {
                // The Scene is empty. This is usually in order to fix "badnavs" or
                // if the character never has been used before. Thus, we proceed to 
                // fetch the default scene.
                // ToDo: Store the ID of the default Scene in the database.
                $default = App::getEntityManager()->find(App::table("Scene"), 1);
                
                if($default === null) {
                    return [
                        "title" => i18n::_("badnav_title", "scene"),
                        "body" => i18n::_("badnav_body", "scene"),
                    ];
                }
                else {
                    $charscene = App::table("CharacterScene");
                    $defaultscene = new $charscene();
                    $defaultscene->setCharacter($character);
                    $defaultscene->setTitle($default->getTitle());
                    $defaultscene->setBody($default->getBody());
                    $character->setScene($defaultscene);
                }
                
                $scene = $character->getScene();
            }
            
            return [
                "title" =>  $scene->getTitle(),
                "body" => $scene->getBody(),
            ];
        }
    }
}
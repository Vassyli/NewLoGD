<?php
/**
 * app/http/middleware/AuthMiddleware.php
 * 
 * @author Basilius Sauter
 * @pacakge App
 * @subpackage Http\Middleware
 */

namespace App\Http\Middleware;

use App\Models\UserModel;
use NewLoGD\Interfaces\MiddlewareHead;
use NewLoGD\Application;
use NewLoGD\Auth;
use NewLoGD\HttpResponse;
use NewLoGD\Session;

/**
 * Description of AuthMiddleware
 */
class AuthMiddleware implements MiddlewareHead {
    /**
     * Creates an user, saves him and returns the id
     * @param array $profile session profile
     * @return int user id
     */
    protected function createUser(array $profile) : int {
        // Create User
        $user = UserModel::_create(
            $profile["name"], 
            $profile["email"], 
            Session::get("auth.provider"), 
            $profile["providerid"]
        );
        
        Application::getEntityManager()->persist($user);
        Application::getEntityManager()->flush();
        
        return $user->getId();
    }
    
    /**
     * Tries to find the user 
     * @param array $users list of users with the correct email address
     * @param array $profile session profile
     * @return int|bool Returns userid or false of none where found.
     */
    public function findUser(array $users, array $profile) {
        foreach($users as $user) {
            if($user->getSocialauth_Type() == Session::get("auth.provider") 
                    and $user->getSocialauth_Id() == $profile["providerid"]) {
                return $user->getId();
            }
        }
        
        return false;
    }
    
    /**
     * Returns the user id if the user has been find or false if no such user exists.
     * @param int $userid The user id to look up
     * @return bool|int False if the user with this id does not exists or else the id
     */
    public function getUser(int $userid) {
        $user = UserModel::_find($userid);
        return $user === NULL?false:$user->getId();
    }
    
    /**
     * Logs the user in
     * @param int $userid The id of the user that needs to be logged in
     */
    public function loginUser(int $userid) {
        Session::put("userid", $userid);
        Auth::setLoginState(Auth::ONLINE);
        Auth::setActiveUser($userid);
    }
    
    /**
     * Decided if the user is authorized, checks if he exists already in the database and 
     * creates him if he doesn't, sets the login state. Also deletes the Session/logs the 
     * user out if he somehow disappeared from the database.
     * 
     * @param HttpResponse $httpresponse The HttpResponse object
     * @return bool True if ok to call next middleware
     */
    public function head(HttpResponse $httpresponse) : bool {
        // Initialize Authentification
        $auth = new Auth(Application::getConfig());
        Application::setAuth($auth);
        
        $userid = Session::get("userid");
        $profile = Session::get("auth.profile");
        
        if(empty($userid) and empty($profile)) {
            // Both userid and profile are empty => user is offline and has not 
            //  tried to login
            
            Auth::setLoginState(Auth::OFFLINE);
            return true;
        }
        elseif(empty($userid)) {
            // Only userid is empty => user is at the login proceure
            
            $users = UserModel::_findByEmail($profile["email"]);
            
            if(count($users) == 0) {
                $userid = $this->createUser($profile);
                $this->loginUser($userid);
                return true;
            }
            else {
                $userid = $this->findUser($users, $profile);
                
                if($userid === false) {
                    Auth::setLoginState(Auth::OFFLINE);
                    $httpresponse->forbidden("[Auth] Only 1 Socialprovider per Emailaddress is allowed.");
                    return false;
                }
                else {
                    $this->loginUser($userid);
                    return true;
                }
            }
        }
        else {
            // userid is non-empty as well - try to found the user
            $userid = $this->getUser($userid);
            if($userid === false) {
                // User disappeared, log him out
                Auth::setLoginState(Auth::OFFLINE);
                return true;
            }
            else {
                $this->loginUser($userid);
                return true;
            }
        }
    }
}

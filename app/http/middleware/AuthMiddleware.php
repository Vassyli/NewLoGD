<?php
/**
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
    public function createUser(array $profile) : int {
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
            if($user->getSocialauth_Provider() == Session::get("auth.provider") 
                    and $user->getSocialauth_Id() == $profile["providerid"]) {
                return $user->getId();
            }
        }
        
        return false;
    }
    
    public function getUser(int $userid) {
        $user = UserModel::_find($userid);
        return $user === NULL?false:$user->getId();
    }
    
    public function loginUser(int $userid) {
        Session::put("userid", $userid);
        Auth::setLoginState(Auth::ONLINE);
        Auth::setActiveUser($userid);
    }
    
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
            
            $users = UserModel::_findByEmail(Session::get($profile["email"]));
            
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

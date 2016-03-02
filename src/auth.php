<?php

namespace NewLoGD;

use App\Models\UserModel;
use Database\User;
use NewLoGD\Application;
use NewLoGD\Config;

const PROVIDER = [
	"google" => "\NewLoGD\Auth\GoogleProvider",
	"facebook" => "\NewLoGD\Auth\FacebookProvider"
];

class Auth {
	protected $config = NULL;
    
    protected static $loginstate = null;
    protected static $activeuser = null;
    
    const ONLINE = 1;
    const OFFLINE = 0;
	
	public function __construct(Config $config) {
		$this->config = $config;
	}
	
	public function getAuthDetails($provider) {
		if(isset(PROVIDER[$provider]) and isset ($this->config["auth"][$provider])) {
			Session::put("auth.provider", $provider);
			
			$providerClass = PROVIDER[$provider];
			$providerClass = new $providerClass($this->config["auth"][$provider]);
			
			return [$providerClass->getAuthUri(), $providerClass->getAuthParams()];
		}
		else {
			return [404, "Not Found"];
		}
	}
	
	public function validateToken($token) {
		$error = false;
		$return = "";
		
		$providerClass = PROVIDER[Session::get("auth.provider")];
		$providerClass = new $providerClass($this->config["auth"][Session::get("auth.provider")]);
		
		if($providerClass->checkAccessToken($token)) {
			Session::put("auth.token", $token);
			Session::put("auth.profile", $providerClass->getProfileData($token));
			
			return "Token OK";
		}
		else {
			return [403, "Access Forbiden"];
		}
	}
	
	public function createNonce() {
		// Replace this with something better later
		$nonce = sha1(session_id().microtime());
		Session::put("auth.nonce", $nonce);
		
		return $nonce;
	}
    
    public static function setLoginState(int $loginstate) {
        self::$loginstate = $loginstate;
    }
    
    public static function getLoginState() {
        return self::$loginstate;
    }
    
    public static function setActiveUser(int $userid) {
        self::$activeuser = $userid;
    }
    
    public static function getActiveUser() {
        return self::$activeuser === NULL ? NULL : UserModel::_find(self::$activeuser);
    }
}
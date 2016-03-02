<?php
/**
 * src/auth/FacebookProvider.php - Contains the Provider for facebook
 *
 * @author Basilius Sauter
 * @package NewLoGD
 * @subpackage Auth
 */

namespace NewLoGD\Auth;

use NewLoGD\Session;

final class FacebookProvider implements ProviderInterface {
	protected $config = [];
	
	public function __construct(array $config) {
		$this->config = $config;
	}
	
	public function getAuthUri() : string {
		return "https://www.facebook.com/dialog/oauth";
	}
	
	public function getAuthParams() : array {
		return [
			"client_id" => $this->config["id"],
			"scope" => $this->config["scope"],
			"state" => "login",
			"response_type" => "token",
			"redirect_uri" => "",
		];
	}
	
	public function checkAccessToken(string $token) : bool {
		// Facebook needs a app access token in addition to the client access token
		$url = sprintf("https://graph.facebook.com/oauth/access_token?client_id=%s&client_secret=%s&grant_type=client_credentials", rawurlencode($this->config["id"]), rawurlencode($this->config["secret"]));
		$content = getUrlContents($url);
		$content = explode("=", $content);
		Session::put("auth.accesstoken", $content[1]);
	
		$url = "https://graph.facebook.com/debug_token?input_token=" . rawurlencode($token) . "&access_token=". rawurlencode($content[1]);
		$content = getUrlContents($url);
		
		if($content === false or isset($content["error_message"]) or isset($content["error"]) or !isset($content["data"]) or !isset($content["data"]["user_id"])) {
			return false;
		}
		
		// Get User-ID
		Session::put("auth.userid", $content["data"]["user_id"]);
		
		return true;
	}
	
	public function getProfileData(string $token) {
		$url = sprintf("https://graph.facebook.com/%s?fields=id,name,email&access_token=%s", Session::get("auth.userid"), rawurlencode(Session::get("auth.accesstoken")));
		$content = getUrlContents($url);
		var_dump($url, $content);
		
		if($content === false or isset($content["error_message"]) or isset($content["error"])) {
			return false;
		}
		
		return [
			"providerid" => $content["id"],
			"name" => $content["name"],
			"email" => $content["email"],
		];
	}
}
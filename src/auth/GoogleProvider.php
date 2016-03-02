<?php
/**
 * src/auth/GoogleProvider.php - Contains the Provider for google api
 *
 * @author Basilius Sauter
 * @package NewLoGD
 * @subpackage Auth
 */

namespace NewLoGD\Auth;

class GoogleProvider implements ProviderInterface {
	protected $config = [];
	
	public function __construct(array $config) {
		$this->config = $config;
	}
	
	public function getAuthUri() : string {
		return "https://accounts.google.com/o/oauth2/v2/auth";
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
		$url = "https://www.googleapis.com/oauth2/v3/tokeninfo?access_token=" . rawurlencode($token);
		$content = getUrlContents($url);
		var_dump($content);
		
		if($content === false or isset($content["error_message"])) {
			return false;
		}
		
		return true;
	}
	
	public function getProfileData(string $token) {
		$url = "https://www.googleapis.com/oauth2/v3/userinfo?access_token=" . rawurlencode($token);
		$content = getUrlContents($url);
		
		if($content === false or  isset($content["error_message"])) {
			return false;
		}
		
		return [
			"providerid" => $content["sub"],
			"name" => $content["name"],
			"email" => $content["email"],
		];
	}
}
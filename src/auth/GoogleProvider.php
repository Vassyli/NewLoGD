<?php
/**
 * src/auth/GoogleProvider.php - Contains the Provider for google api
 *
 * @author Basilius Sauter
 * @package NewLoGD
 * @subpackage Auth
 */

namespace NewLoGD\Auth;

/**
 * Provides the ability for OAuth with Google
 */
class GoogleProvider implements ProviderInterface {
    /** @var array Configuration details for Google */
	protected $config = [];
	
    /**
     * Constructor
     * @param array $config Configuration details for Google
     */
	public function __construct(array $config) {
		$this->config = $config;
	}
	
    /**
     * Returns the api uri of Google that handels oauth requests
     * @return string OAuth uri
     */
	public function getAuthUri() : string {
		return "https://accounts.google.com/o/oauth2/v2/auth";
	}
	
    /**
     * Returns parameters needed to successfully identify the user at google
     * @return array OAuth parameters (@link /config/auth.php)
     */
	public function getAuthParams() : array {
		return [
			"client_id" => $this->config["id"],
			"scope" => $this->config["scope"],
			"state" => "login",
			"response_type" => "token",
			"redirect_uri" => "",
		];
	}
	
    /**
     * Tries to verify the validity of the token sent by the user at Google
     * @param string $token The token to verify
     * @return bool True of verification is ok, False if not
     */
	public function checkAccessToken(string $token) : bool {
		$url = "https://www.googleapis.com/oauth2/v3/tokeninfo?access_token=" . rawurlencode($token);
		$content = getUrlContents($url);
		var_dump($content);
		
		if($content === false or isset($content["error_message"])) {
			return false;
		}
		
		return true;
	}
	
    /**
     * Returns normalized informations about the user profile stored at the provider
     * @param string $token
     * @return bool|array false if facebook returns something errornous, an array returning providerid, name and email if successful
     */
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
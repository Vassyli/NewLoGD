<?php
/**
 * src/auth/ProviderInterface.php - Contains the Interface for Auth-Providers
 *
 * @author Basilius Sauter
 * @package NewLoGD
 * @subpackage Auth
 */

namespace NewLoGD\Auth;

/**
 * Returns the content of a uri using curl
 * @param string $uri The uri that should get fetched
 * @return bool|string|array False if request wasn't successfull, string if json_decode wasn't successfull, else an array
 */
function getUrlContents(string $uri) {
	$options = [
		\CURLOPT_URL => $uri,
		\CURLOPT_HTTPGET => true,
		\CURLOPT_RETURNTRANSFER => true,
	];
	
	$curl = \curl_init();
	\curl_setopt_array($curl, $options);
	$content = \curl_exec($curl);
	
	if($content === false) {
		return false;
	}
	else {
		$json = \json_decode($content, true);
	}
	
	if($json === NULL) {
		return $content;
	}
	
	\curl_close($curl);
	
	return $json;
}

/**
 * Provides a common interface for Auth providers
 */
interface ProviderInterface {
	/**
	 * Returns the uri for authentification
	 * @return string authentification uri
	 */
	public function getAuthUri() : string;
	/**
	 * Returns additional parameters needed getting a token
	 * @return array authentification parameters
	 */
	public function getAuthParams() : array;
	/**
	 * Validates if a access token is valid
	 * @param string $token Token
	 * @return bool true if it is valid, false if not
	 */
	public function checkAccessToken(string $token) : bool;
	/**
	 * Returns Profile data
	 * @param string $token Token
	 * @return array profile data
	 */
	 public function getProfileData(string $token);
}
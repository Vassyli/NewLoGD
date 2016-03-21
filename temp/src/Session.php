<?php
/**
 * src/session.php - Session management class
 *
 * @author Basilius Sauter
 * @package NewLoGD
 */

namespace NewLoGD;

use NewLoGD\Config;

/**
 * Session management class
 *
 * This class starts, loads and ends a Session. Furthermore, it provides
 * support for so-called "flash variables" that only survice 1 page call
 */
class Session {
	/** @var array session storage (saved until session expires) */
	protected static $session;
	/** @var array flash storage (1 pagehit only) */
	protected static $flash;
	
	/**
	 * Initializes a session
	 *
	 * @param Config $config Configuration object
	 */
	public static function initialize(Config $config) {
		\session_name($config["app"]["id"]);
		\session_start();
		
		// Prepare Session
		if(!isset($_SESSION["storage"])) {
			$_SESSION["storage"] = [];
		}
		self::$session = &$_SESSION["storage"];
		
		// Save flash variables and remove them from storage
		if(isset($_SESSION["flash"])) {
			self::$flash = $_SESSION["flash"];
		}
		else {
			self::$flash = [];
		}
		$_SESSION["flash"] = [];
	}
	
	/**
	 * Retrieves a $key/$val pair by $key
	 * @param string $key the key of the stored variable
	 * @return mixed The Content of the requested variable or NULL if it not exists
	 */
	public static function get(string $key) {
		return self::$session[$key] ?? NULL;
	}
	
	/**
	 * Saves a $key/$val pair in the session
	 * @param string $key The key
	 * @param mixed $val The value the key is set to
	 */
	public static function put(string $key, $val) {
		self::$session[$key] = $val;
	}
	
	/**
	 * Retrieves $key/$val pair stored in flash storage
	 * @param string $key the key of the stored variable
	 * @return mixed The Content of the requested variable or NULL if it not exists
	 */
	public static function flashGet(string $key) {
		return self::$flash[$key] = $val ?? $_SESSION["flash"][$key] ?? NULL;
	}
	
	/**
	 * Saves a $key/$val pair in the flash storage
	 * @param string $key The key
	 * @param mixed $val The value the key is set to
	 */
	public static function flashPut(string $key, $val) {
		$_SESSION["flash"][$key] = $val;
	}
    
    /**
     * Drops the whole session.
     */
    public static function drop() {
        $params = \session_get_cookie_params();
        setcookie(\session_name(), '', \time() - 42000, $params["path"],
            $params["domain"], $params["secure"], $params["httponly"]
        );
    
        $_SESSION = [];
        
        \session_destroy();
    }
}
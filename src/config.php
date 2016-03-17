<?php
/**
 * Configuration manager
 *
 * @author Basilius Sauter
 * @package NewLoGD
 */
declare(strict_types=1);

namespace NewLoGD;

/**
 * Config class for managing application configurations
 *
 * The Configuration class loads and manages different configuration files
 * stored in $root/config/*. It provides an ArrayAccess-Interface to 
 * easily call the configured properties.
 * 
 * Properties so far are:
 *   - app, for general configuration of the application
 *   - db, for configuration of Doctrine
 */
class Config implements \ArrayAccess {
	/** @var array configuration */
	protected $config = null;
	
	/** Constructor */
	public function __construct() {}
	
	/**
	 * Loads configuration files (so far only selected ones)
	 */
	public function load() {
		if (null !== $this->config) {
			return;
		}
		
		$this->config = [
			"app" => require __DIR__ . "/../config/app.php",
			"auth" => require __DIR__ . "/../config/auth.php",
			"db" => require __DIR__ . "/../config/db.php",
		];
	}
	
	/**
	 * Checks if a given offset exists in $this->config
	 *
	 * @param string $offset
	 */
	public function offsetExists($offset) {
		return isset($this->config[$offset]);
	}
	
	/**
	 * Returns a configuration set stored in $this->config
	 * @param string $offset offset
	 * @return array subconfiguration
	 */
	public function offsetGet($offset) {
		return $this->config[$offset];
	}
	
	/**
	 * Configuration is Read-Only at runtime, do not use.
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {}
	/**
	 * Configuration is Read-Only at runtime, do not use.
	 * @param string $offset
	 */
	public function offsetUnset($offset) {}
}
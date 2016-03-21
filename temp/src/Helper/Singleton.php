<?php
/**
 * src/helper/Singleton.php - A Singleton trait
 */

namespace NewLoGD\Helper;

/**
 * Allows a class to use the singleton pattern
 */
trait Singleton {
    /** @var instance of this class */
    protected static $__instance = NULL;
    
    /** protected constructor */
    protected function __construct() {
    }
    
    /**
     * Returns the single object of this class
     * @return self The object
     */
    public static function getInstance() : self {
        // Does this class already have a single object? If not, create one.
        if(self::$__instance === null) {
            self::$__instance = new self();
        }
        
        return self::$__instance;
    }
}
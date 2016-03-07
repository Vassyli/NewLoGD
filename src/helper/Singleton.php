<?php

namespace NewLoGD\Helper;

trait Singleton {
    protected static $__instance = NULL;
    
    protected function __construct() {
    }
    
    public static function getInstance() {
        if(self::$__instance === null) {
            self::$__instance = new self();
        }
        
        return self::$__instance;
    }
}
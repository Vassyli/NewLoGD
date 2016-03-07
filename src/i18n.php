<?php

namespace NewLoGD;

use NewLoGD\Helper\Singleton;

class i18n {
    use Singleton;
    
    public static $languageStack = [];
    
    public function init() {
        $accept = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        foreach($accept as $language) {
            $lang = explode(";", $language)[0];
            
            // Try to load a stack of languages
            if($this->languageExists($lang)) {
                self::$languageStack[] = $this->loadLanguage($lang);
            }
        }
    }
    
    public function getLanguageFilename(string $language) : string {
        $lang = explode("-", $language);
        $lang_main = substr(preg_replace("#[^\p{Ll}]#u", "", $lang[0]), 0, 2);
        $lang_sub = isset($lang[1]) ? substr(preg_replace("#[^\p{Lu}]#u", "", $lang[1]), 0, 2) : "";
        
        if(empty($lang_sub)) {
            $filename = "i18n/translation_{$lang_main}.php";
        }
        else {
            $filename = "i18n/translation_{$lang_main}_{$lang_sub}.php";
        }
        
        return $filename;
    }
    
    public function languageExists(string $language) : bool {
        $filename = $this->getLanguageFilename($language);
        return file_exists($filename);
    }
    
    public function loadLanguage(string $language) : array {
        return include $this->getLanguageFilename($language);
    }
    
    public static function _(string $identifier, string $scheme = "", array $arguments = []) {
        foreach(self::$languageStack as $language) {
            if(isset($language[$scheme]) and isset($language[$scheme][$identifier])) {
                return vsprintf($language[$scheme][$identifier], $arguments);
            }
        }
        
        return "((" . $scheme. "|". $identifier . "))";
    }
}
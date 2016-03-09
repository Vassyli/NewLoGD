<?php

namespace NewLoGD;

use NewLoGD\Helper\Singleton;

class i18n {
    use Singleton;
    
    /** @var array $languageStack A language files with decreasing priority */
    public static $languageStack = [];
    
    public static $languagesLoaded = [];
    
    /**
     * Initializes i18n
     * 
     * This method takes the HTTP_ACCEPT_LANGUAGE value and tries to find the 
     * desired user languages. The language files are then stored inside of 
     * $this->languageStack with decreasing priority.
     */
    public function init() {
        $accept = !empty($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ? explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]) : ["en"];
        
        foreach($accept as $language) {
            $lang = explode(";", $language)[0];
            // Canoncialize language
            $lang_canon = \Locale::canonicalize($lang);
            $primary_language = \Locale::getPrimaryLanguage($lang_canon);
            
            // Try to load a stack of languages
            if($this->languageExists($primary_language) and !in_array($primary_language, self::$languagesLoaded)) {
                self::$languagesLoaded[] = $primary_language;
                self::$languageStack[] = $this->loadLanguage($primary_language);
            }
        }
    }
    
    /**
     * Returns the filename of a language file depending on primary language 
     * and region.
     * @param string $language
     * @return string
     */
    public function getLanguageFilename(string $language) : string {
        $lang = explode("-", $language);
        $lang_primary = substr(preg_replace("#[^\p{Ll}]#u", "", $lang[0]), 0, 2);
        $lang_region = isset($lang[1]) ? substr(preg_replace("#[^\p{Lu}]#u", "", $lang[1]), 0, 2) : "";
        
        if(empty($lang_sub)) {
            $filename = "i18n/translation_{$lang_primary}.php";
        }
        else {
            $filename = "i18n/translation_{$lang_primary}_{$lang_region}.php";
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
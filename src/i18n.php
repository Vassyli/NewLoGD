<?php
/**
 * newlogd/i18n.php - Internationalization utility
 */

namespace NewLoGD;

use NewLoGD\Helper\Singleton;

/**
 * Provides utilities for internationaliztion (i18n)
 */
class i18n {
    use Singleton;
    
    /** @var array $languageStack All language files with decreasing priority */
    public static $language_stack = [];
    
    /** @var array $langaguesLoaded A list of all languages loaded */
    public static $languages_loaded = [];
    
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
            if($this->languageExists($primary_language) and !in_array($primary_language, self::$languages_loaded)) {
                self::$languages_loaded[] = $primary_language;
                self::$language_stack[] = $this->loadLanguage($primary_language);
            }
        }
    }
    
    /**
     * Returns the filename of a language file depending on primary language 
     * and region.
     * @param string $language The language to convert to a filename
     * @return string The filename of the desired language
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
    
    /**
     * Checks if a language exists
     * @param string $language The language to check
     * @return bool True if it exists, False if it doesn't
     */
    public function languageExists(string $language) : bool {
        $filename = $this->getLanguageFilename($language);
        return file_exists($filename);
    }
    
    /**
     * Loads a language file and returns its content
     * @param string $language The language to load
     * @return array The translation array from the language
     */
    public function loadLanguage(string $language) : array {
        return include $this->getLanguageFilename($language);
    }
    
    /**
     * Translates a given string
     * @param string $identifier String identifier
     * @param string $context Translation context of the string
     * @param array $arguments Additional arguments that are supplied to the string for vsprintf
     * @return string The translated string (or a generated string (($context|$identifier)) to see untranslated strings)
     */
    public static function _(string $identifier, string $context = "", array $arguments = []) : string {
        foreach(self::$language_stack as $language) {
            if(isset($language[$context]) and isset($language[$context][$identifier])) {
                return vsprintf($language[$context][$identifier], $arguments);
            }
        }
        
        return "((" . $context. "|". $identifier . "))";
    }
}
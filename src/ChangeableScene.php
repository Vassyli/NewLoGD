<?php

namespace NewLoGD;

use Database\{CharacterScene, SceneExtension};
use NewLOGD\Auth;

class ChangeableScene {
    const WIDGET_LIST = 1;
    const WIDGET_SIMPLEFORM = 2;
    
    private $character_scene = NULL;
    private $extension = "";
    private $scene_extension;
            
    public function __construct(CharacterScene $character_scene, SceneExtension $extension) {
        $this->character_scene = $character_scene;
        $this->extension = $this->fqcn2extension(get_called_class());
        $this->scene_extension = $extension;
    }
    
    /*
     * Converts fulli qualified class names to the name of the extension.
     */
    private function fqcn2extension(string $fqcn) {
        $classparts = explode("\\", $fqcn);
        $extension = NULL;
        
        foreach($classparts as $index => $part) {
            if($part == "Extensions") {
                $extension = $classparts[$index+1];
            }
        }
        
        if($extension === NULL) {
            throw new \Exception("[ChangeableScene] Cannot resolve full qualified class name to extension name (${fqcn})");
        }
        
        return $extension;
    }
    
    protected function getExtension() {
        return $this->scene_extension;
    }
    
    protected function setTitle(string $title) {
        
    }
    
    protected function addParagraph(string $text) {
        $this->character_scene->addParagraph($text);
    }
    
    protected function clearScene() {}
    protected function clearTitle() {}
    protected function clearDescription() {}
    
    protected function addWidget(int $type, array $options = []) {
        $map = [
            self::WIDGET_LIST => "addListWidget",
            self::WIDGET_SIMPLEFORM => "addSimpleformWidget",
        ];
        
        call_user_func([$this, $map[$type]], $options);
    }
    
    protected function getCurrentCharacter() {
        return Auth::getActiveUser()->getCurrentCharacter();
    }
    
    private function createOptionString(array $options) {
        if(count($options) > 0) {
            $option_prestring = [];
            foreach($options as $option_name => $option_value) {
                $option_prestring[] = is_int($option_name) ? $option_value : $option_name . ":" . $option_value;
            }
            
            $option_string = "|" . implode("|", $option_prestring);
        }
        else {
            $option_string = "";
        }
        
        return $option_string;
    }
    
    private function addListWidget(array $options) {
        $this->character_scene->addParagraph(sprintf("@{%s|List%s}", $this->extension, $this->createOptionString($options)));
    }
    
    private function addSimpleformWidget(array $options) {
        $this->character_scene->addParagraph(sprintf("@{%s|SimpleForm%s}", $this->extension, $this->createOptionString($options)));
    }
}
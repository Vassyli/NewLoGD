<?php

namespace Extensions\Commentary;

use Database\CharacterScene;
use NewLoGD\ChangeableScene;

class Scene extends ChangeableScene {
    public function change() {
        $this->addParagraph("Some people are talking to each other.");
        
        $this->getCurrentCharacter()->set("ext.Commentary.section", $this->getExtension()->get("section", "village"));
        $this->getCurrentCharacter()->set("ext.Commentary.maxlength", $this->getExtension()->get("maxlength", 255));
        
        $this->addWidget(self::WIDGET_LIST, [
            "reversed",
        ]);
        $this->addWidget(self::WIDGET_SIMPLEFORM, [
            "text" => "Chat with other people",
            "submit" => "Submit",
            "name" => "comment", 
            "maxlength" => 255
        ]);
        #$this->addParagraph("@{Commentary|List|comment}");
        #$this->addParagraph("@{Commentary|SimpleForm|name:comment|maxlength:255}");
        #$this->addAction();
    }
}
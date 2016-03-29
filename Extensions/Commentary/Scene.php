<?php

namespace Extensions\Commentary;

use Database\CharacterScene;

class Scene {
    public function change(CharacterScene $c_scene) {
        $c_scene->addParagraph("Some people are talking to each other.");
        $c_scene->addParagraph("@{Commentary|List|comment}");
        $c_scene->addParagraph("@{Commentary|SimpleForm|name:comment|maxlength:255}");
        $c_scene->addAction();
    }
}
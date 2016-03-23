<?php

namespace Extensions\Commentary\Http\Controllers;

use App\Http\Controllers\Controller;
use NewLoGD\{Application, Auth};

class CommentaryController extends Controller {
    public function all() {
        Auth::getActiveUser()->getCurrentCharacter()->set("test_i", 6);
        Auth::getActiveUser()->getCurrentCharacter()->set("test_d", 6.0);
        Auth::getActiveUser()->getCurrentCharacter()->set("test_s", "6");
        return ["hallo", Auth::getActiveUser()->getCurrentCharacter()->get("displayname")];
    }
}
<?php

namespace Extensions\Commentary\Http\Controllers;

use App\Http\Controllers\Controller;
use NewLoGD\{Application, Auth};
use NewLoGD\Exceptions\{invalidData, RequestForbidden};

use Extensions\Commentary\Database\Comment;

class CommentaryController extends Controller {
    public function count() {
        $numOfComments = Comment::countAll("village");
        return ["count" => $numOfComments];
    }
    
    public function all($page = 0, $limit = 30) {
        $section = $this->getCurrentCharacter()->get("ext.Commentary.section", "village");
        
        $comments = Comment::getBySection($section, $page*$limit, $limit);
        $numOfComments = Comment::countAll($section);
        $return = ["count" => $numOfComments, "comments" => []];
        
        foreach($comments as $comment) {
            $author = $comment->getAuthor();
            $return["comments"][] = [
                "author" => is_null($author) ? NULL : $author->getName(),
                "authorid" => is_null($author) ? NULL : $author->getId(),
                "line" => $comment->getFinalComment(),
                "date" => $comment->getCreatedAt()->getTimestamp(),
                "section" => $comment->getSection(),
            ];
        }
        
        return $return;
    }
    
    public function post() {
        $character = $this->getCurrentCharacter();
        $section = $character->get("ext.Commentary.section", NULL);
        
        if($section === NULL) {
            throw new RequestForbidden();
        }
        
        try {
            $comment = Comment::create([
                "author" => $character,
                "section" => $section,
                "line" => $_POST["comment"]??"",
                "maxlength" => $character->get("ext.Commentary.maxlength", 255),
            ]);
            
            return "OK?";
        }
        catch(\Exception $e) {
            throw new invalidData($e->getMessage());
        }
    }
}
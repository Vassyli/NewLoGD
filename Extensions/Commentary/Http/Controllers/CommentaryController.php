<?php

namespace Extensions\Commentary\Http\Controllers;

use App\Http\Controllers\Controller;
use NewLoGD\{Application, Auth};

use Extensions\Commentary\Database\Comment;

class CommentaryController extends Controller {
    public function all() {
        $comments = Application::getEntityManager()->getRepository(Comment::class)->findBy(["section" => "village"], ["createdAt" => "ASC"]);
        $return = [];
        
        foreach($comments as $comment) {
            $return[] = [
                "author" => $comment->getAuthor()->getName(),
                "authorid" => $comment->getAuthor()->getId(),
                "comment" => $comment->getFinalComment(),
                "date" => $comment->getCreatedAt()->getTimestamp(),
                "section" => $comment->getSection(),
            ];
        }
        
        return $return;
    }
}
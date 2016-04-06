<?php

namespace Extensions\Commentary\Database;

use Database\Character;
use NewLoGD\Application;

use function NewLoGD\Helper\normalizeLineBreaks;

/**
 * ORM for Character table
 * @Entity
 * @Table(name="Comments")
 */
class Comment {
    const EMOTE_NONE = 0;
    const EMOTE_3RD = 1;
    const EMOTE_ENV = 2;
    
    /**
     * @var int Primary id
	 * @Id @Column(type="integer") @GeneratedValue
	 */
    private $id;
    
    /** 
     * @var \Database\Character Character id
     * @ManyToOne(targetEntity="\Database\Character") 
     */
    private $author;
    
    /** 
     * @var array Comment content
     * @Column(type="text") 
     */
    private $body = "";
    
    /**
     * @var int Emote type
     * @Column(type="smallint")
     */
    private $emote = 0;
    
    /**
     * @var string section
     * @Column(type="string")
     */
    private $section = "";
    
    /**
     * @var \DateTime Time of post creation
     * @Column(type="datetime", name="created_at")
     */
    private $createdAt;
    
    public function __construct() {
        $this->createdAt = new \DateTime("now");
    }
    
    public function getId() { return $this->id; }
    
    public function getAuthor() { return $this->author; }
    public function setAuthor(Character $author) { $this->author = $author; }
    
    public function getBody() : string { return $this->body; }
    public function setBody(string $body) { $this->body = normalizeLineBreaks($body); }
    
    public function getEmote() : int { return $this->emote; }
    public function setEmote(int $emote) { $this->emote = $emote; }
    
    public function getSection() : string { return $this->section; }
    public function setSection(string $section) { $this->section = $section; }
    
    public function getCreatedAt() { return $this->createdAt; }
    
    public function getFinalComment() {
        $author = $this->getAuthor();
        if(is_null($author)) {
            return $this->getBody();
        }
        
        switch($this->getEmote()) {
            case self::EMOTE_ENV:
                return " (". $this->getAuthor(). ")" . $this->getBody();
                break;
            case self::EMOTE_3RD:
                return $this->getAuthor()->getName() . " " .$this->getBody();
                break;
            case self::EMOTE_NONE:
            default:
                return $this->getAuthor()->getName() ." says \"". $this->getBody() ."\"";
                break;
        }
    }
    
    public static function getBySection($section, $offset = NULL, $limit = NULL) {
        return Application::getEntityManager()->getRepository(Comment::class)->findBy(["section" => $section], ["createdAt" => "DESC"], $limit, $offset);
    }
    
    public static function countAll($section) {
        $query = Application::getEntityManager()->createQueryBuilder()
            ->select("COUNT(t.id)")
            ->from(self::class, "t")
            ->where("t.section = :section")
            ->setParameter("section", $section)
            ->getQuery();
        return $query->getSingleScalarResult();
    }
    
    public static function create($variables) {
        if($variables["line"] === "") {
            throw new \Exception("Commentary line should not be empty.");
        }
        
        $comment = new Comment();
        $comment->setSection($variables["section"]);
        $comment->setAuthor($variables["author"]);
        $comment->setLine($variables["line"], $variables["maxlength"]);
        
        Application::getEntityManager()->persist($comment);
    }
    
    public function setLine(string $line, int $maxlength) {
        if(\mb_substr($line, 0, 3) === "/me") {
            $this->setEmote(self::EMOTE_3RD);
            $start = 3;
            $end = $maxlength+3;
        }
        elseif(\mb_substr($line, 0, 2) === "/X") {
            $this->setEmote(self::EMOTE_ENV);
            $start = 3;
            $end = $maxlength+2;
        }
        else {
            $this->setEmote(self::EMOTE_NONE);
            $start = 0;
            $end = $maxlength;
        }
        
        
        $this->setBody(\trim(\mb_substr($line, $start, $end)));
    }
}
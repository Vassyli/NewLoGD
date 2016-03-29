<?php

namespace Extensions\Commentary\Database;

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
        $this->createdAt = new DateTime("now");
    }
    
    public function getId() { return $this->id; }
    
    public function getAuthor() { return $this->author; }
    public function setAuthor(Character $character) { $this->character = $character; }
    
    public function getBody() : string { return $this->body; }
    public function setBody(string $body) { $this->body = normalizeLineBreaks($body); }
    
    public function getEmote() : int { return $this->emote; }
    public function setEmote(int $emote) { $this->emote = $emote; }
    
    public function getSection() : string { return $this->section; }
    public function setSection(string $section) { $this->section = $section; }
    
    public function getCreatedAt() { return $this->createdAt; }
    
    public function getFinalComment() {
        switch($this->getEmote()) {
            case self::EMOTE_ENV:
                return $this->getBody();
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
}
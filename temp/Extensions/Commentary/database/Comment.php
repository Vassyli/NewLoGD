<?php

namespace Extensions\Commentary\Database;

/**
 * ORM for Character table
 * @Entity
 * @Table(name="Comments")
 */
class Comment {
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
     * @var array Scene Description
     * @Column(type="text") 
     */
    private $body;
}
<?php
/**
 * database/user.php - Doctrine ORM class
 *
 * @author Basilius Sauter
 * @package Database
 */

namespace Database;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ORM for User-Table
 * @Entity
 * @Table(name="Users")
 */
class User implements \JsonSerializable {
	/**
     * @var int Primary user id
	 * @Id @Column(type="integer") @GeneratedValue
	 */
	private $id;
	
	/**
     * @var string User name (unique)
	 * @Column(type="string")
	 */
	private $name;
	
	/**
     * @var string user password (or null of none has been set)
	 * @Column(type="string", nullable=True)
	 */
	private $password;
	
	/**
     * @var string E-mail address (unique)
	 * @Column(type="string")
	 */
	private $email;
	
	/**
     * @var string Socialauth type (Facebook, Google)
	 * @Column(type="string", nullable=True)
	 */
	private $socialauth_type;
	
	/**
     * @var string The provider-specific user id
	 * @Column(type="string", nullable=True)
	 */
	private $socialauth_id;
    
    /**
     * @var array<\Database\Characters> List of database characters
     * @OneToMany(targetEntity="Character", mappedBy="owner")
     */
    private $characters;
    
    /**
     * @var \Database\Character current selected character
     * @OneToOne(targetEntity="Character")
     */
    private $current_character = NULL;
    
    /** Definition of other default values */
    public function __construct() {
        $this->characters = new ArrayCollection();
    }
	
    /** 
     * Returns primary id
     * @return int ID
     */
	public function getId() { return $this->id; }
	
    /**
     * Returns the name.
     * @return string The name
     */
	public function getName() { return $this->name; }
    /**
     * Sets the name
     * @param string $name The name
     */
	public function setName(string $name){ $this->name = $name; }
	
    /**
     * Returns the password
     * @return string The password
     */
	public function getPassword() { return $this->password; }
    /**
     * Sets the password
     * @param string $password
     */
	public function setPassword(string $password) { $this->password = $password; }
	
    /**
     * Gets the email
     * @return string The email.
     */
	public function getEmail() { return $this->email; }
    /**
     * Sets the email
     * @param string $email
     */
	public function setEmail(string $email){ $this->email = $email; }
	
    /**
     * Returns socialauth type (facebook, google ...)
     * @return string Socialauth type
     */
	public function getSocialauth_type() { return $this->socialauth_type; }
    /**
     * Sets socialauth type
     * @param string $socialauth_type Socialauth type (Facebook, Google ...)
     */
	public function setSocialauth_type(string $socialauth_type) { $this->socialauth_type = $socialauth_type; }
	
    /**
     * Returns the Socialauth id
     * @return string Socialauth id
     */
	public function getSocialauth_id() { return $this->socialauth_id; }
    /**
     * Sets the Socialauth id
     * @param string $socialauth_id The Socialauth id
     */
	public function setSocialauth_id(string $socialauth_id) { $this->socialauth_id = $socialauth_id; }
    
    /**
     * Returns a list of characters
     * @return array list of characters
     */
    public function getCharacters() { return $this->characters; }
    /**
     * Adds a character to this user entry
     * @param \Database\Character $character The Character
     */
    public function addCharacter(Character $character) {
        $this->characters->add($character);
        $character->setOwner($this);
    }
    
    /**
     * Gets the current character or NULL if none has been defined
     * @return \Database\Character the character.
     */
    public function getCurrentCharacter() { return $this->current_character; }
    /**
     * Sets the current character of this user.
     * @param \Database\Character $character
     */
    public function setCurrentCharacter(Character $character) {
        $this->current_character = $character;
    }
    
    /**
     * Returns an array with all important information about this user.
     * @return array User informations
     */
    public function jsonSerialize() : array {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "password" => $this->password,
            "email" => $this->email,
            "socialauth_type" => $this->socialauth_type,
            "socialauth_id" => $this->socialauth_id,
        ];
    }
}
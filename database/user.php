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
	 * @Id @Column(type="integer") @GeneratedValue
	 */
	private $id;
	
	/**
	 * @Column(type="string")
	 */
	private $name;
	
	/**
	 * @Column(type="string", nullable=True)
	 */
	private $password;
	
	/**
	 * @Column(type="string")
	 */
	private $email;
	
	/**
	 * @Column(type="string", nullable=True)
	 */
	private $socialauth_type;
	
	/**
	 * @Column(type="string", nullable=True)
	 */
	private $socialauth_id;
    
    /**
     * @OneToMany(targetEntity="Character", mappedBy="owner")
     */
    private $characters;
    
    public function __construct() {
        $this->characters = new ArrayCollection();
    }
	
	public function getId() { return $this->id; }
	
	public function getName() { return $this->name; }
	public function setName($name){ $this->name = $name; }
	
	public function getPassword() { return $this->password; }
	public function setPassword($password) { $this->password = $password; }
	
	public function getEmail() { return $this->email; }
	public function setEmail($email){ $this->email = $email; }
	
	public function getSocialauth_type() { return $this->socialauth_type; }
	public function setSocialauth_type($socialauth_type) { $this->socialauth_type = $socialauth_type; }
	
	public function getSocialauth_id() { return $this->socialauth_id; }
	public function setSocialauth_id($socialauth_id) { $this->socialauth_id = $socialauth_id; }
    
    public function getCharacters() { return $this->characters; }
    
    public function jsonSerialize() {
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
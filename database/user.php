<?php
/**
 * database/user.php - Doctrine ORM class
 *
 * @author Basilius Sauter
 * @package Database
 */

namespace Database;

/**
 * ORM for User-Table
 * @Entity
 * @Table(name="Users")
 */
class User implements \JsonSerializable {
	/**
	 * @Id @Column(type="integer") @GeneratedValue
	 */
	protected $id;
	
	/**
	 * @Key @Column(type="string")
	 */
	protected $name;
	
	/**
	 * @Column(type="string", nullable=True)
	 */
	protected $password;
	
	/**
	 * @Column(type="string")
	 */
	protected $email;
	
	/**
	 * @Column(type="string", nullable=True)
	 */
	protected $socialauth_type;
	
	/**
	 * @Column(type="string", nullable=True)
	 */
	protected $socialauth_id;
	
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
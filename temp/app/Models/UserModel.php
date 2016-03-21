<?php
/**
 * app/models/UserModel.php - User Management Model
 * 
 * @author Basilius Sauter
 * @package App
 * @subpackage Http/Models
 */

namespace App\Models;

use NewLoGD\Application;
use Database\User;

/**
 * Provides a Model for editing and viewing users
 */
class UserModel extends Model {
	/** @var array list of public column names */
	public static $public = ["id", "name"];
	
    /**
     * Returns a list of users matching the email address
     * @param string $value The email address to look up.
     * @return array<\Database\User> List of User entries matching the email address
     */
	public static function _findByEmail(string $value) {
		$qb = Application::getEntityManager()->createQueryBuilder();
        $qb->select("u")
            ->from(self::ormName(), "u")
            ->where("u.email = :email");
        $query = $qb->getQuery();
		$query->setParameter(":email", $value);
		$users = $query->getResult();
        
        return $users;
	}
    
    /**
     * Creates a new entry in the User database
     * @param string $name Name of the user
     * @param string $email E-mail of the user
     * @param string $socialauth_type Socialauth provider (facebook, google)
     * @param string $socialauth_id Provider specific user id
     * @return \Database\User A User entity
     */
    public static function _create(string $name, string $email, string $socialauth_type, string $socialauth_id) : User {
        $orm = self::ormName();
        $user = new $orm();
        $user->setName($name);
        $user->setEmail($email);
        $user->setSocialauth_type($socialauth_type);
        $user->setSocialauth_id($socialauth_id);
        
        return $user;
    }
}
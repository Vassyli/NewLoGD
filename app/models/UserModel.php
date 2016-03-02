<?php
/**
 * app/models/UserModel.php - User Management Model
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
	
	public static function _findByEmail($value) {
		$qb = Application::getEntityManager()->createQueryBuilder();
        $qb->select("u.id")
            ->from(self::ormName(), "u")
            ->where("u.email = :email");
        $query = $qb->getQuery();
		$query->setParameter(":email", $value);
		$users = $query->getResult();
        
        return $users;
	}
    
    public static function _create($name, $email, $socialauth_type, $socialauth_id) : User {
        $orm = self::ormName();
        $user = new $orm();
        $user->setName($name);
        $user->setEmail($email);
        $user->setSocialauth_type($socialauth_type);
        $user->setSocialauth_id($socialauth_id);
        
        return $user;
    }
}
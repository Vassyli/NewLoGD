<?php
/**
 * app/models/CharacterModel.php - User Management Model
 * 
 * @author Basilius Sauter
 * @package App
 * @subpackage Http/Models
 */

namespace App\Models;

use NewLoGD\Application;
use Database\Character;

/**
 * Provides a Model for editing and viewing characters
 */
class CharacterModel extends Model {
	/** @var array list of public column names */
	public static $public = ["id", "name", "level"];
	
    /**
     * Returns a list of Database\Character that match the name $value
     * @param string $value The name that needs to be looked up
     * @return mixed NULL if no name has been found, a array<\Database\Character> if yes.
     */
    public static function _findByName(string $value) {
		$qb = Application::getEntityManager()->createQueryBuilder();
        $qb->select("t")
            ->from(self::ormName(), "t")
            ->where("t.name = :name");
        $query = $qb->getQuery();
		$query->setParameters(["name" => $value]);
		
        $result = $query->getResult();
        
        return $result;
	}
    
    /**
     * Creates a new Character and returns a Instance of Database\Character
     * @param string $name Name of the Character
     * @return Character
     */
    public static function _create(string $name) : Character {
        $orm = self::ormName();
        
        $entry = new $orm();
        $entry->setName($name);
        
        return $entry;
    }
}
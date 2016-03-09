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
	
    public static function _findByName($value) {
		$qb = Application::getEntityManager()->createQueryBuilder();
        $qb->select("t")
            ->from(self::ormName(), "t")
            ->where("t.name = :name");
        $query = $qb->getQuery();
		$query->setParameters(["name" => $value]);
		
        $result = $query->getResult();
        
        return $result;
	}
    
    public static function _create($name) : Character {
        $orm = self::ormName();
        
        $entry = new $orm();
        $entry->setName($name);
        
        return $entry;
    }
}
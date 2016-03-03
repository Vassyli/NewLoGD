<?php
/**
 * app/models/Model.php - Base Model
 *
 * All Models have to derive from this abstract class. Methods with a leading underscore (_) are 
 * only for internal use, never to get delivered to the output! Use 
 * <code>
 * 		$entry = $model->getPublicFields($entry)
 * </code>
 * in order to filter all non-public fields.
 * @author Basilius Sauter
 * @package App
 * @subpackage Models
 */

namespace App\Models;

use NewLoGD\Application;

/**
 * Basic Model class
 *
 * This class provides basic methods for deriving models.
 */
abstract class Model {
	/** @var array $public A list of column names the model is allowed to expose to public */
	public static $public = [];
	/** @var string $ormname Contains the classname for the derived ORM class */
	protected static $ormname = NULL;
	
	/**
     * Returns the fully-qualified name of the ORM class derived from the calling class' name.
	 * UserModel, for example, gets converted to Database\User
	 */
	protected static function ormName() {
		if(empty($ormname)) {
			$ormname = explode("\\", get_called_class());
			$ormname = array_pop($ormname);
			
			if(substr($ormname, -5) == "Model") {
				$ormname = substr($ormname, 0, -5);
			}
			
			self::$ormname = $ormname;
			return "Database\\".$ormname;
		}
		else {
			return "Database\\".self::$ormname;
		}
	}
	
	/**
	 * Only shows columns of a given entry that are in self::$public
	 * @return array filtered entry
	 */
	public static function getPublicFields($entry) {
		$e = [];
		foreach(get_called_class()::$public as $field) {
			$method = "get".$field;
			$e[$field] = $entry->$method();
		}
		return $e;
	}
	
	/**
	 * Fetches all entries of a model from the database
	 * @return array An array of entries found
	 */
	public static function all() {
		$repository = Application::getEntityManager()->getRepository(self::ormName());
		$entries = $repository->findAll();
		$return = [];
		
		foreach($entries as $entry) {
			$return[] = self::getPublicFields($entry);
		}
		
		return $return;
	}
	
	/**
	 * Tries to find a entry in the table given by the primary id
	 * @return mixed Found entry with filtered fields
	 */
	public static function find(int $id) {
		$entry = self::_find($id);
		return ($entry === NULL) ? NULL : self::getPublicFields($entry);
    }
    
    /**
	 * Tries to find a entry in the table given by the primary id
	 * @return mixed Object with the found entry or NULL if no entry has been found
	 */
    public static function _find(int $id) { 
        return Application::getEntityManager()->find(self::ormName(), $id);
    }
}
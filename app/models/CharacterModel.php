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
	

}
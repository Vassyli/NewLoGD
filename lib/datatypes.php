<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

class Datatypes {
    const TYPE_LINE             = 0b0000000000000001;
	const TYPE_PASSWORD         = 0b0000000000000010;
	const TYPE_EMAIL            = 0b0000000000000100;
    
    const TYPE_FULLTEXT         = 0b0000000010000000;
	
	const TYPE_SUBMIT           = 0b0000000100000000;
	const TYPE_RESET            = 0b0000001000000000;
	
	const TYPEGROUP_VARCHAR     = 0b0000000000000111;
	const TYPEGROUP_BUTTON      = 0b0000001100000000;
}
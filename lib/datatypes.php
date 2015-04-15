<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

class Datatypes {
    const TYPE_LINE             = 0b00000000000000000000000000000001; // 1
	const TYPE_PASSWORD         = 0b00000000000000000000000000000010; // 2
	const TYPE_EMAIL            = 0b00000000000000000000000000000100; // 3
    
    const TYPE_FULLTEXT         = 0b00000000000000000000000010000000; // 128
    
    const TYPE_BITFIELD         = 0b00001000000000000000000000000000; // 134217728
	
	const TYPE_SUBMIT           = 0b00100000000000000000000000000000; // 536870912
	const TYPE_RESET            = 0b01000000000000000000000000000000; // 1073741824
	
	const TYPEGROUP_VARCHAR     = 0b00000000000000000000000000000111; // 6
	const TYPEGROUP_BUTTON      = 0b01100000000000000000000000000000; // 1610612736
}
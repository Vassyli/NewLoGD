<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

/**
 * Contains and manages Session
 */
class Session implements Submodel {
	private $model;
	
	public function __construct(Model $model) {
		$this->model = $model;
	}
	
	public function start() {
		session_name(LOGD_SESSIONNAME);
		session_start();
		\debug("<b style=\"color: green;\">Session started</b>");
	}
	
	public function stop() {
		session_write_close();
		\debug("<b style=\"color: green;\">Session write and close.</b>");
	}
}

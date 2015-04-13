<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */
 
 namespace Submodel;

/**
 * Contains and manages Session
 */
class Session implements SubmodelInterface {
	private $model;
	
	public function __construct(\Model $model) {
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
	
	public function is_loggedin() {
		return $this->get_sessionval("loggedin", false) ? true : false;
	}
	
	public function login() {
		$this->set_sessionval("loggedin", true);
	}
	
	public function logout() {
		$this->set_sessionval("loggedin", false);
	}
	
	public function clear() {
		session_destroy();
		// ToDo: Delete session cookie.
	}
	
	public function set_active_account($id) {
		$this->set_sessionval("active_account", $id);
	}
	
	public function get_active_account() {
		return $this->get_sessionval("active_account", 0);
	}
	
	protected function get_sessionval($key, $default = NULL) {
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
	}
	
	protected function set_sessionval($key, $val) {
		$_SESSION[$key] = $val;
	}
}

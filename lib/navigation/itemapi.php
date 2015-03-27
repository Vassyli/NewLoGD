<?php

namespace Navigation;

interface ItemAPI {
	public function get_id();
	public function get_parentid();
	public function get_action();
	public function get_title();
}
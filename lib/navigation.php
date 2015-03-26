<?php

class DE_Navigation {
	protected $rows = array();
	
	public function __construct() {
	}
	
	public static function getby_pageid(Model $model, $pageid) {
		$result = $model->from("navigation")->where("page_id", $pageid)->orderby("parentid");
		
		while($row = $result->fetch()) {
			var_dump($row);
		}
	}
}
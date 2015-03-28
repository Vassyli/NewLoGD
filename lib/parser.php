<?php

class Parser {
	public function __construct() {
	}
	
	public function parse($text) {
		$text = str_replace("\r\n", "\n", $text);
		$text = "<p>".str_replace("\n\n", "</p>\n<p>", $text)."</p>";
		return $text;
	}
}
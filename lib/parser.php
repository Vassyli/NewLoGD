<?php

class Parser {
	public function __construct() {
	}
	
	public function parse($text) {
		$text = str_replace("\r\n", "\n", $text);
		$textparts = explode("\n\n", $text);
		$text = "";
		foreach($textparts as $part) {
			$part = trim($part);  // There is no mb_trim and there is no need for it.
			
			if(mb_substr($part, 0, 1) != "<" and mb_strlen($part) > 0) {
				// Ignore empty strings and lines starting with a html tag.
				$text.="<p>".$part."</p>\n\n";
			}
			else {
				$text.=$part;
			}
		}
		//$text = "<p>".str_replace("\n\n", "</p>\n<p>", $text)."</p>";
		return $text;
	}
}
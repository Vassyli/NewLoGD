<?php

header("Content-type: text/plain");

function out($inp = "") {
	print($inp);
	print("\n");
}

out("Test-Script\n");
out();

$search = "user/{id}/{id}";

$matches = [
	"user/1",
	"user/5",
	"user/200",
	"user/92837428934789",
	"user/b13",
	"user/user/13",
	"user/page/13",
	"user/13/16/18",
	"user/12/18",
	"user/hi/9",
	"user/9/500",
];

$pattern = str_replace("{id}", "([0-9]+)", $search);
var_dump($pattern);

foreach($matches as $match) {
	$yarr = [];
	preg_match_all("#^".$pattern."$#", $match, $yarr);
	var_dump($yarr);
	//var_dump($search, $match, $pattern, preg_match("#^".$pattern."$#", $match) == true);
}
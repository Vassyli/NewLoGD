<?php

header("Content-type: text/plain; charset=utf-8");

/*$array = array(
	"name_fieldname" => "Name des Benutzerkontos",
	"password1_fieldname" => "Dein Passwort",
	"password2_fieldname" => "Dein Passwort (bestätigen)",
	"email1_fieldname" => "Deine E-Mailadresse",
	"email2_fieldname" => "Deine E-Mailadresse (bestätigen)",
	"submitbutton_name" => "Registrierung bestätigen",
);*/

$array = [
    "enum" => [
        0b0001 => "Anonymer Zugriff erlaubt",
        0b0010 => "Zugriff auf Account-Level erlaubt",
        0b0100 => "Zugriff auf Charakter-Ebene erlaubt, sofern Navigation möglich",
        0b1000 => "Zugriff auf Charakter-Ebene immer erlaubt",
    ]
];
    
print json_encode($array, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_BIGINT_AS_STRING);

print "\n";

$unsafe = "Hel lö12
 3'\"\n_hi";
\var_dump(preg_replace("/[^\p{L}0-9_-]/u", '', $unsafe));

/*
print "\n";
var_dump(decbin(0b10000000 & 0b11000000));
var_dump(decbin(0b00000001 & 0b11000000));

$a = 2147483647;
$b = -2147483648;
$c = (1 << 1) | (1 << 2) | (1 << 4) | (1 << 31);

print($c)."\n";
print(decbin($c))."\n";*/
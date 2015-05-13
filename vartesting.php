<?php

header("Content-type: text/plain; charset=utf-8");

$array = [
    "options" => [
        "range" => [
            "min" => -1000,
            "max" => 1000,
        ]
    ]
    /*"foreign" => [
        "table" => "navigations",
        "key" => "id",
        "method" => "getId",
        "display" => ["id", "title"],
    ],
    "validator" => [
        "nullifempty" => true,
        
    ]*/
];

function test(){ return ["A", "B", "C"]; }
list($a, $b, $c) = test();
var_dump($a, $b, $c);

print "\n";
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
<?php

namespace NewLoGD\Helper;

/**
 * @param string $input
 * @return string
 */
function normalizeLineBreaks(string $input) : string {
    $strReplace = [
        "\r\n" => "\n",
        "\r" => "\n"
    ];
    
    
    return \str_replace(array_keys($strReplace), array_values($strReplace), $input);
}
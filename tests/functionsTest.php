<?php

namespace Tests;

use function NewLoGD\Helper\normalizeLineBreaks;

class FunctionTests extends \PHPUnit_Framework_TestCase {
    public function testNormalizeLineBreaks() {
        $mac9 = "\r";
        $unix = "\n";
        $wind = "\r\n";
        
        $tests = [
            "Hello${mac9}World" => "Hello${unix}World",
            "Hello${wind}World" => "Hello${unix}World",
            "Hello${unix}World" => "Hello${unix}World",
            "Hello${mac9}${mac9}World" => "Hello${unix}${unix}World",
            "Hello${wind}${wind}World" => "Hello${unix}${unix}World",
            "Hello${wind}my${mac9}dear${unix}World" => "Hello${unix}my${unix}dear${unix}World"
        ];
        
        foreach($tests as $actual => $expected) {
            var_dump($expected, $actual, normalizeLineBreaks($actual));
            $this->assertEquals($expected, normalizeLineBreaks($actual));
        }
    }
}
<?php

namespace App\Regex;

class SimpleRegex extends \PHPUnit\Framework\TestCase
{
    public function testPregMatch() {
        $identify = 'Hello there';
        $pattern = '/there/';
        $search = preg_match($pattern, $identify);
        $this->assertSame(1, $search);
    }

    public function testPregMatchInsensitive() {
        $identifyString = 'Hello THERE';
        $pattern = '/there/i';
        $search = preg_match($pattern, $identifyString);
        $this->assertSame(1, $search);
    }

    public function testNumbersFromString() {
        $urlPath = 'He11o th3er3';
        $pattern = '/\d+/';
        preg_match_all($pattern, $urlPath, $matches);
        $this->assertEquals($matches, [[
            11, 3, 3
        ]]);
    }

    public function testExtractionOfWordsOnly() {
        $randomText = 'Hello there stranger 111 111 111.';
        $pattern = '/[a-z]+/i';
        preg_match_all($pattern, $randomText, $matches);
        $this->assertEquals($matches, [[
            'Hello',
            'there',
            'stranger'
        ]]);
    }

    public function testExtractionOfWordsWithDigits() {
        $randomText = '(124)-1234-123';
        $pattern = '/^[(]\d{3}[)][-][\d]{4}[-][\d]{3}$/';
        $result = preg_match($pattern, $randomText);
        $this->assertEquals(1, $result);
    }

    public function testValidateUrl() {
        $url = 'https://moricas.net.com';
        $pattern = '/((https):\/\/+[a-z]+\.+[a-z]+.[a-z]+)/';
        $result = preg_match($pattern, $url);
        $this->assertEquals(1, $result);
    }

    public function testUrlWithPatterns() {
        $url = "https://www.stefanlaiu.com?hello=user&name=stefan";
        $pattern = '/(http|https):\/\/[A-Za-z]{3}.[A-Za-z]+.[A-Za-z]{3}\?/';
        $patternToGetOnlyParams = '/\?+(.*)/';
        $this->assertEquals(1, preg_match($pattern, $url));
        $this->assertEquals(1, preg_match($patternToGetOnlyParams, $url));
    }

}
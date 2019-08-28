<?php

namespace Wszetko\Sitemap\Tests;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Helpers\Url;

class HelpersTest extends TestCase
{
    public function testCheckDomainValids()
    {
        $testCaseValid = [
            'a',
            'a.b',
            'localhost',
            'example.',
            'example.c',
            'example.co',
            'example.com',
            'example.info',
            'example.com.pl',
            'example.co.uk',
            'example1.com',
            '1example.com',
            'ex-am-ple.com',
            'żółw.pl',
            'sub.example.com',
            'sub.example.com',
            'sub.sub.example.com',
            'xn--fsqu00a.xn--0zwm56d'
        ];

        foreach ($testCaseValid as $test) {
            $this->assertTrue(Url::checkDomain($test),
                "Test 'CheckDomain' for '$test' should return TRUE. FALSE was returned.");
        }
    }

    public function testCheckDomainInvalids()
    {
        $testCaseInvalid = [
            '',
            ' ',
            '-',
            '.',
            '..',
            '0',
            'alert(',
            '<script',
            '.example.com',
            'example-.com',
            'example.com/',
            'example..com',
            'example.com ',
            ' example.com',
            'ex ample.com'
        ];

        foreach ($testCaseInvalid as $test) {
            $this->assertFalse(Url::checkDomain($test),
                "Test 'CheckDomain' for '$test' should return FALSE. TRUE was returned.");
        }
    }

    public function testNormalizeUrlValids()
    {
        $testCasesValid = [
            'https://example.com',
            'https://example.com/',
            'http://example.com',
            'ftp://example.com',
            'https://1.2.3.4',
            'https://example.com:80',
            'https://example.com:8080',
            'https://user@example.com',
            'https://user:password@example.com',
            'https://example.com/path',
            'https://example.com/path.html',
            'https://example.com/path/path',
            'https://example.com/path/path.php',
            'https://example.com?param=value',
            'https://example.com?param=value&param2=value',
            'https://example.com#anchor',
            'https://example.com?param=value#anchor',
            'https://example.com/path?param=value#anchor',
            'https://user:password@example.com:8080/path/path.html?param=value&param2=value#anchor'
        ];

        foreach ($testCasesValid as $test) {
            $this->assertEquals($test, Url::normalizeUrl($test),
                "Test 'NormalizeUrl' for '$test' should return '$test'.");
        }
    }

    public function testNormalizeUrlInvalids()
    {
        $testCasesInvalid = [
            'http://',
            'http://.',
            'http://..',
            'http://../',
            'http://?',
            'http://??',
            'http://??/',
            'http://#',
            'http://##',
            'http://##/',
            'http://foo.bar?q=Spaces should be encoded',
            '//',
            '//a',
            '///a',
            '///',
            'http:///a',
            'foo.com',
            'rdar://1234',
            'h://test',
            'http:// shouldfail.com',
            ':// should fail',
            'http://foo.bar/foo(bar)baz quux',
            'ftps://foo.bar/',
            'http://-error-.invalid/',
            'http://-a.b.co',
            'http://a.b-.co',
            'http://0.0.0.0',
            'http://3628126748',
            'http://.www.foo.bar/',
            'http://www.foo.bar./',
            'http://.www.foo.bar./',
            'https://user@password@example.com'
        ];

        foreach ($testCasesInvalid as $test) {
            $this->assertFalse(false, Url::normalizeUrl($test), "Test 'NormalizeUrl' for '$test' should return FALSE.");
        }
    }

    public function testNormalizeUrlNormalized()
    {
        $testCaseNormalized = [
            'https://żółw.pl' => 'https://xn--w-uga1v8h.pl',
            'https://例如.中国' => 'https://xn--fsqu6v.xn--fiqs8s',
            'http://مثال.إختبار' => 'http://xn--mgbh0fb.xn--kgbechtv',
            'https://user:pass@zółw.pl' => 'https://user:pass@xn--zw-5ja03a.pl',
            'https://example.com/żółty' => 'https://example.com/żółty',
        ];

        foreach ($testCaseNormalized as $test => $result) {
            $this->assertEquals($result, Url::normalizeUrl($test),
                "Test 'NormalizeUrl' for '$test' should return '$result'.");
        }
    }
}
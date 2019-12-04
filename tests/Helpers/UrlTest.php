<?php

declare(strict_types=1);

/**
 * This file is part of Wszetko Sitemap.
 *
 * (c) Paweł Kłopotek-Główczewski <pawelkg@pawelkg.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Wszetko\Sitemap\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Helpers\Url;

/**
 * Class HelpersTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class UrlTest extends TestCase
{
    /**
     * @dataProvider validUrlProvider
     *
     * @param string $url
     */
    public function testNormalizeUrlValids(string $url)
    {
        $this->assertEquals($url, Url::normalizeUrl($url));
    }

    public function validUrlProvider()
    {
        return [
            ['https://example.com'],
            ['https://example.com/'],
            ['http://example.com'],
            ['ftp://example.com'],
            ['ftps://foo.bar/'],
            ['https://1.2.3.4'],
            ['https://example.com:80'],
            ['https://example.com:8080'],
            ['https://user@example.com'],
            ['https://user:password@example.com'],
            ['https://example.com/path'],
            ['https://example.com/path.html'],
            ['https://example.com/path/path'],
            ['https://example.com/path/path.php'],
            ['https://example.com?param=value'],
            ['https://example.com?param=value&param2=value'],
            ['https://example.com#anchor'],
            ['https://example.com?param=value#anchor'],
            ['https://example.com/path?param=value#anchor'],
            ['https://user:password@example.com:8080/path/path.html?param=value&param2=value#anchor'],
            ['http://[2001:0db8:85a3:0000:0000:8a2e:0370:7334]/'],
            ['http://[2001:0db8:85a3::8a2e:0370:7334]/'],
            ['http://0.0.0.0'],
        ];
    }

    /**
     * @dataProvider invalidUrlProvider
     *
     * @param string $uri
     */
    public function testNormalizeUrlInvalids(string $uri)
    {
        $this->assertFalse(Url::normalizeUrl($uri));
    }

    public function invalidUrlProvider()
    {
        return [
            ['http://.'],
            ['http://..'],
            ['http://'],
            ['http://?'],
            ['http://??'],
            ['http://??/'],
            ['http://#'],
            ['http://##'],
            ['http://##/'],
            ['//'],
            ['//a'],
            ['///a'],
            ['///'],
            ['http:///a'],
            ['foo.com'],
            ['http:// shouldfail.com'],
            [':// should fail'],
            ['http://-error-.invalid/'],
            ['http://-a.b.co'],
            ['http://a.b-.co'],
            ['http://.www.foo.bar/'],
            ['http://www.foo.bar./'],
            ['http://.www.foo.bar./'],
            ['https://toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong.com'],
        ];
    }

    /**
     * @dataProvider normalizeUrlProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testNormalizeUrlNormalized(string $input, string $expected)
    {
        $this->assertEquals($expected, Url::normalizeUrl($input));
    }

    public function normalizeUrlProvider()
    {
        return [
            ['https://żółw.pl', 'https://xn--w-uga1v8h.pl'],
            ['https://例如.中国', 'https://xn--fsqu6v.xn--fiqs8s'],
            ['http://مثال.إختبار', 'http://xn--mgbh0fb.xn--kgbechtv'],
            ['https://user:pass@zółw.pl', 'https://user:pass@xn--zw-5ja03a.pl'],
            ['https://example.com/żółty', 'https://example.com/%C5%BC%C3%B3%C5%82ty'],
            ['https://example.com/test/../index.html', 'https://example.com/index.html'],
            ['https://example.com/test/./index.html', 'https://example.com/test/index.html'],
            ['https://example.com?q=Spaces should be encoded', 'https://example.com?q=Spaces%20should%20be%20encoded'],
            ['https://example.com/foo(bar)baz quux', 'https://example.com/foo(bar)baz%20quux']
        ];
    }
}

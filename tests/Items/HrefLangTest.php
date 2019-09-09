<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items\HrefLang;

/**
 * Class HrefLangTest
 *
 * @package Wszetko\Sitemap\Tests
 */
class HrefLangTest extends TestCase
{
    public function testConstructor()
    {
        $hrefLang = new HrefLang('pl-PL', '/');
        $this->assertInstanceOf(HrefLang::class, $hrefLang);

        $hrefLang = new HrefLang('fr-be', '/');
        $this->assertInstanceOf(HrefLang::class, $hrefLang);

        $hrefLang = new HrefLang('en', '/');
        $this->assertInstanceOf(HrefLang::class, $hrefLang);

        $hrefLang = new HrefLang('x-default', '/');
        $this->assertInstanceOf(HrefLang::class, $hrefLang);

        $hrefLang = new HrefLang('zh-Hant', '/');
        $this->assertInstanceOf(HrefLang::class, $hrefLang);

        $hrefLang = new HrefLang('zh-Hans', '/');
        $this->assertInstanceOf(HrefLang::class, $hrefLang);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('hrefLang need to be set.');
        new HrefLang('PL', '/');
    }

    public function testHrefLang()
    {
        $hrefLang = new HrefLang('pl-PL', '/');
        $hrefLang->setDomain('https://example.com');

        $expectedResult = [
            'pl-PL' => ['href' => 'https://example.com/']
        ];

        $this->assertEquals($expectedResult, $hrefLang->getHrefLang());

        $hrefLang = new HrefLang('pl-PL', '/');
        $hrefLang->setDomain('https://example.com');
        $hrefLang->addHrefLang('en', '/en');

        $expectedResult = [
            'pl-PL' => ['href' => 'https://example.com/'],
            'en' => ['href' => 'https://example.com/en']
        ];

        $this->assertEquals($expectedResult, $hrefLang->getHrefLang());
    }

    public function testToArray()
    {
        $hrefLang = new HrefLang('pl-PL', '/');
        $hrefLang->setDomain('https://example.com');

        $expectedResult = [
            '_namespace' => $hrefLang::NAMESPACE_NAME,
            '_element' => 'link',
            'link' => [
                [
                    '_attributes' => [
                        'rel' => 'alternate',
                        'hreflang' => 'pl-PL',
                        'href' => 'https://example.com/'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedResult, $hrefLang->toArray());
    }
}

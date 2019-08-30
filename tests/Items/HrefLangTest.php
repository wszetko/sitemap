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
        $HrefLang = new HrefLang('pl-PL', '/');
        $this->assertInstanceOf(HrefLang::class, $HrefLang);

        $HrefLang = new HrefLang('fr-be', '/');
        $this->assertInstanceOf(HrefLang::class, $HrefLang);

        $HrefLang = new HrefLang('en', '/');
        $this->assertInstanceOf(HrefLang::class, $HrefLang);

        $HrefLang = new HrefLang('x-default', '/');
        $this->assertInstanceOf(HrefLang::class, $HrefLang);

        $HrefLang = new HrefLang('zh-Hant', '/');
        $this->assertInstanceOf(HrefLang::class, $HrefLang);

        $HrefLang = new HrefLang('zh-Hans', '/');
        $this->assertInstanceOf(HrefLang::class, $HrefLang);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid hreflang parameter.');
        $HrefLang = new HrefLang('PL', '/');
    }

    public function testHrefLang()
    {
        $HrefLang = new HrefLang('pl-PL', '/');
        $HrefLang->addHrefLang('en', '/en');
        $HrefLang->setDomain('https://example.com');

        $expectedResult = [
            'pl-PL' => 'https://example.com/',
            'en' => 'https://example.com/en'
        ];

        $this->assertEquals($expectedResult, $HrefLang->getHrefLangs());
    }

    public function testToArray()
    {
        $HrefLang = new HrefLang('pl-PL', '/');
        $HrefLang->setDomain('https://example.com');

        $expectedResult = [
            '_namespace' => $HrefLang::NAMESPACE_NAME,
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

        $this->assertEquals($expectedResult, $HrefLang->toArray());
    }
}

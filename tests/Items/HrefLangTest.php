<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;

/**
 * Class HrefLangTest
 *
 * @package Wszetko\Sitemap\Tests
 */
class HrefLangTest extends TestCase
{
    public function testConstructor()
    {
        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('pl-PL', '/');
        $this->assertInstanceOf(\Wszetko\Sitemap\Items\HrefLang::class, $HrefLang);

        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('fr-be', '/');
        $this->assertInstanceOf(\Wszetko\Sitemap\Items\HrefLang::class, $HrefLang);

        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('en', '/');
        $this->assertInstanceOf(\Wszetko\Sitemap\Items\HrefLang::class, $HrefLang);

        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('x-default', '/');
        $this->assertInstanceOf(\Wszetko\Sitemap\Items\HrefLang::class, $HrefLang);

        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('zh-Hant', '/');
        $this->assertInstanceOf(\Wszetko\Sitemap\Items\HrefLang::class, $HrefLang);

        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('zh-Hans', '/');
        $this->assertInstanceOf(\Wszetko\Sitemap\Items\HrefLang::class, $HrefLang);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid hreflang parameter.');
        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('PL', '/');
    }

    public function testHrefLang()
    {
        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('pl-PL', '/');
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
        $HrefLang = new \Wszetko\Sitemap\Items\HrefLang('pl-PL', '/');
        $HrefLang->setDomain('https://example.com');

        $expectedResult =  [
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
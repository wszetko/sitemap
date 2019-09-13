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

namespace Wszetko\Sitemap\Tests\Items;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items\HrefLang;

/**
 * Class HrefLangTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class HrefLangTest extends TestCase
{
    /**
     * @dataProvider constructorProvider
     *
     * @param string $hrefLang
     * @param string $href
     *
     * @throws \ReflectionException
     */
    public function testConstructor(string $hrefLang, string $href)
    {
        $test = new HrefLang($hrefLang, $href);
        $this->assertInstanceOf(HrefLang::class, $test);
    }

    /**
     * @return array
     */
    public function constructorProvider()
    {
        return [
            ['pl-PL', '/'],
            ['fr-be', '/'],
            ['en', '/'],
            ['x-default', '/'],
            ['zh-Hant', '/'],
            ['zh-Hans', '/'],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('hrefLang need to be set.');
        new HrefLang('PL', '/');
    }

    /**
     * @throws \ReflectionException
     */
    public function testHrefLang()
    {
        $hrefLang = new HrefLang('pl-PL', '/');
        $hrefLang->setDomain('https://example.com');

        $expectedResult = [
            'pl-PL' => ['href' => 'https://example.com/'],
        ];

        $this->assertEquals($expectedResult, $hrefLang->getHrefLang());

        $hrefLang = new HrefLang('pl-PL', '/');
        $hrefLang->setDomain('https://example.com');
        $hrefLang->addHrefLang('en', '/en');

        $expectedResult = [
            'pl-PL' => ['href' => 'https://example.com/'],
            'en' => ['href' => 'https://example.com/en'],
        ];

        $this->assertEquals($expectedResult, $hrefLang->getHrefLang());
    }

    /**
     * @throws \ReflectionException
     */
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
                        'href' => 'https://example.com/',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedResult, $hrefLang->toArray());
    }
}

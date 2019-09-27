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

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;
use Wszetko\Sitemap\Items\Mobile;

/**
 * Class UrlTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class UrlTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $url = new Items\Url('test');
        $this->assertInstanceOf(Items\Url::class, $url);
    }

    /**
     * @throws \ReflectionException
     */
    public function testPropertyNotExists()
    {
        $url = new Items\Url('test');
        $wrong = 'setNotExists';
        $this->assertNull($url->$wrong('test'));
    }

    /**
     * @dataProvider domainProvider
     *
     * @param mixed $domain
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testDomain($domain, $expected)
    {
        $url = new Items\Url('test');
        $url->setDomain($domain);
        $this->assertEquals($expected, $url->getDomain());
    }

    /**
     * @throws \ReflectionException
     */
    public function testDomainException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Domain name is not valid.');
        $url = new Items\Url('test');
        $url->setDomain('broken|domain');
    }

    /**
     * @return array
     */
    public function domainProvider()
    {
        return [
            ['https://example.com', 'https://example.com'],
            ['https://example.com/', 'https://example.com'],
        ];
    }

    /**
     * @dataProvider getLocProvider
     *
     * @param mixed $loc
     * @param mixed $excepted
     *
     * @throws \ReflectionException
     */
    public function testGetLoc($loc, $excepted)
    {
        $url = new Items\Url($loc);
        $url->setDomain('https://example.com');
        $this->assertEquals($excepted, $url->getLoc());
    }

    /**
     * @return array
     */
    public function getLocProvider()
    {
        return [
            ['test', 'https://example.com/test'],
            ['/test', 'https://example.com/test'],
            ['test/path', 'https://example.com/test/path'],
            ['test/path', 'https://example.com/test/path'],
            ['test.html', 'https://example.com/test.html'],
            ['test.html?param=value', 'https://example.com/test.html?param=value'],
        ];
    }

    /**
     * @dataProvider lastModProvider
     *
     * @param mixed $lastMod
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testLastMod($lastMod, $expected)
    {
        $url = new Items\Url('test');
        $url->setLastmod($lastMod);
        $this->assertEquals($expected, $url->getLastmod());
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function lastModProvider()
    {
        date_default_timezone_set('Europe/London');

        return [
            [null, null],
            [new DateTime('2013-11-16'), '2013-11-16'],
            [new DateTime('2013-11-16 19:00'), '2013-11-16T19:00:00+00:00'],
            [new DateTime('0000-00-00 00:00'), null],
            ['2013-12-11', '2013-12-11'],
        ];
    }

    /**
     * @dataProvider changeFreqProvider
     *
     * @param mixed $changeFreq
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testChangeFreq($changeFreq, $expected)
    {
        $url = new Items\Url('test');
        $url->setChangefreq($changeFreq);
        $this->assertEquals($expected, $url->getChangefreq());
    }

    /**
     * @return array
     */
    public function changeFreqProvider()
    {
        return [
            ['invalid', null],
            ['always', 'always'],
            ['hourly', 'hourly'],
            ['daily', 'daily'],
            ['weekly', 'weekly'],
            ['monthly', 'monthly'],
            ['yearly', 'yearly'],
            ['never', 'never'],
        ];
    }

    /**
     * @dataProvider priorityProvider
     *
     * @param mixed $priority
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testPriority($priority, $expected)
    {
        $url = new Items\Url('test');
        $url->setPriority($priority);
        $this->assertEquals($expected, $url->getPriority());
    }

    /**
     * @return array
     */
    public function priorityProvider()
    {
        return [
            [1, '1.0'],
            [0, '0.0'],
            [.5, '0.5'],
            [2, null],
            [-1, null],
            ['1', '1.0'],
            ['0', '0.0'],
            ['.5', '0.5'],
            ['2', null],
            ['-1', null],
            [new \stdClass(), null],
            ['test', null],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddExtensions()
    {
        $url = new Items\Url('test');
        $url->setDomain('https://example.com');

        $extension = new Mobile();

        $url->addExtension($extension);

        $expectedResult = [
            $extension::NAMESPACE_NAME => [$extension],
        ];

        $this->assertEquals($expectedResult, $url->getExtensions());
    }

    /**
     * @throws \ReflectionException
     */
    public function testToArray()
    {
        date_default_timezone_set('Europe/London');

        $url = new Items\Url('test');
        $url->setDomain('https://example.com');
        $url->setLastmod(new DateTime('2013-11-16'));
        $url->setChangefreq('always');
        $url->setPriority(1);
        $extension = new Mobile();
        $url->addExtension($extension);
        $extension->setDomain($url->getDomain());

        $expectedResult = [
            'url' => [
                'loc' => 'https://example.com/test',
                'lastmod' => '2013-11-16',
                'changefreq' => 'always',
                'priority' => '1.0',
                $extension::NAMESPACE_NAME => [$extension->toArray()],
            ],
        ];

        $this->assertEquals($expectedResult, $url->toArray());
    }
}

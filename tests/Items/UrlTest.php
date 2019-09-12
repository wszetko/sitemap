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

namespace Wszetko\Sitemap\Tests;

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
 * @coversNothing
 */
class UrlTest extends TestCase
{
    public function testConstructor()
    {
        $url = new Items\Url('test');

        $this->assertInstanceOf(Items\Url::class, $url);
    }

    public function testPropertyNotExists()
    {
        $url = new Items\Url('test');
        $this->assertNull($url->setNotExists('test'));
    }

    public function testDomain()
    {
        $url = new Items\Url('test');
        $url->setDomain('https://example.com');

        $this->assertEquals('https://example.com', $url->getDomain());

        $url->setDomain('https://example.com/');

        $this->assertEquals('https://example.com', $url->getDomain());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Domain name is not valid.');

        $url->setDomain('broken|domain');
    }

    public function testGetLoc()
    {
        $url = new Items\Url('test');
        $url->setDomain('https://example.com');

        $this->assertEquals('https://example.com/test', $url->getLoc());
    }

    public function testLastMod()
    {
        date_default_timezone_set('Europe/London');

        $url = new Items\Url('test');
        $this->assertNull($url->getLastmod());

        $url = new Items\Url('test');
        $url->setLastmod(new DateTime('2013-11-16'));
        $this->assertEquals('2013-11-16', $url->getLastmod());

        $url = new Items\Url('test');
        $url->setLastmod(new DateTime('2013-11-16 19:00'));
        $this->assertEquals('2013-11-16T19:00:00+00:00', $url->getLastmod());

        $url = new Items\Url('test');
        $url->setLastmod(new DateTime('0000-00-00 00:00'));
        $this->assertNull($url->getLastmod());

        $url = new Items\Url('test');
        $url->setLastmod('2013-12-11');
        $this->assertEquals('2013-12-11', $url->getLastmod());
    }

    public function testChangeFreq()
    {
        $tests = [
            ['input' => 'invalid', 'expected' => null],
            ['input' => 'always', 'expected' => 'always'],
            ['input' => 'hourly', 'expected' => 'hourly'],
            ['input' => 'daily', 'expected' => 'daily'],
            ['input' => 'weekly', 'expected' => 'weekly'],
            ['input' => 'monthly', 'expected' => 'monthly'],
            ['input' => 'yearly', 'expected' => 'yearly'],
            ['input' => 'never', 'expected' => 'never'],
        ];

        foreach ($tests as $test) {
            $url = new Items\Url('test');
            $url->setChangefreq($test['input']);
            $this->assertEquals($test['expected'], $url->getChangefreq());
        }
    }

    public function testPriority()
    {
        $tests = [
            ['input' => 1, 'expected' => '1.0'],
            ['input' => 0, 'expected' => '0.0'],
            ['input' => .5, 'expected' => '0.5'],
            ['input' => 2, 'expected' => null],
            ['input' => -1, 'expected' => null],
            ['input' => '1', 'expected' => '1.0'],
            ['input' => '0', 'expected' => '0.0'],
            ['input' => '.5', 'expected' => '0.5'],
            ['input' => '2', 'expected' => null],
            ['input' => '-1', 'expected' => null],
            ['input' => new \stdClass(), 'expected' => null],
            ['input' => 'test', 'expected' => null],
        ];

        foreach ($tests as $test) {
            $url = new Items\Url('test');
            $url->setPriority($test['input']);
            $this->assertEquals($test['expected'], $url->getPriority());
        }
    }

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

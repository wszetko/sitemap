<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;
use Wszetko\Sitemap\Items\Mobile;

/**
 * Class UrlTest
 *
 * @package Wszetko\Sitemap\Tests
 */
class UrlTest extends TestCase
{
    public function testConstructor()
    {
        $url = new Items\Url('test');

        $this->assertInstanceOf(Items\Url::class, $url);
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
        $this->assertNull($url->getLastMod());

        $url = new Items\Url('test');
        $url->setLastMod(new DateTime('2013-11-16'));
        $this->assertEquals('2013-11-16', $url->getLastMod());

        $url = new Items\Url('test');
        $url->setLastMod(new DateTime('2013-11-16 19:00'));
        $this->assertEquals('2013-11-16T19:00:00+00:00', $url->getLastMod());

        $url = new Items\Url('test');
        $url->setLastMod(new DateTime('0000-00-00 00:00'));
        $this->assertNull($url->getLastMod());

        $url = new Items\Url('test');
        $url->setLastMod('2013-12-11');
        $this->assertEquals('2013-12-11', $url->getLastMod());
    }

    public function testChangeFreq()
    {
        $url = new Items\Url('test');
        $url->setChangeFreq('invalid');
        $this->assertNull($url->getChangeFreq());

        $url = new Items\Url('test');
        $url->setChangeFreq('always');
        $this->assertEquals('always', $url->getChangeFreq());
    }

    public function testPriority()
    {
        $url = new Items\Url('test');
        $url->setPriority(1);
        $this->assertEquals('1.0', $url->getPriority());

        $url = new Items\Url('test');
        $url->setPriority(0);
        $this->assertEquals(0.0, $url->getPriority());

        $url = new Items\Url('test');
        $url->setPriority(.5);
        $this->assertEquals('0.5', $url->getPriority());

        $url = new Items\Url('test');
        $url->setPriority(2);
        $this->assertEquals(null, $url->getPriority());

        $url = new Items\Url('test');
        $url->setPriority(-1);
        $this->assertEquals(null, $url->getPriority());
    }

    public function testAddExtensions()
    {
        $url = new Items\Url('test');
        $url->setDomain('https://example.com');

        $extension = new Mobile();

        $url->addExtension($extension);

        $expectedResult = [
            $extension::NAMESPACE_NAME => $extension
        ];

        $this->assertEquals($expectedResult, $url->getExtensions());
    }

    public function testToArray()
    {
        date_default_timezone_set('Europe/London');

        $url = new Items\Url('test');
        $url->setDomain('https://example.com');
        $url->setLastMod(new DateTime('2013-11-16'));
        $url->setChangeFreq('always');
        $url->setPriority(1);
        $extension = new Mobile();
        $url->addExtension($extension);
        $extension->setDomain($url->getDomain());

        $expectedResult = [
            'loc' => 'https://example.com/test',
            'lastmod' => '2013-11-16',
            'changefreq' => 'always',
            'priority' => '1.0',
            $extension::NAMESPACE_NAME => $extension->toArray()
        ];

        $this->assertEquals($expectedResult, $url->toArray());
    }
}

<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;

/**
 * Class UrlTest
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @todo    : Extensions Test
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
        $url->setDomain('https://example.com');

        $url->setLastMod(new DateTime('2013-11-16'));
        $this->assertEquals('2013-11-16', $url->getLastMod());

        $url->setLastMod(new DateTime('2013-11-16 19:00'));
        $this->assertEquals('2013-11-16T19:00:00+00:00', $url->getLastMod());
    }

    public function testChangeFreq()
    {
        $url = new Items\Url('test');
        $url->setDomain('https://example.com');

        $url->setChangeFreq('invalid');
        $this->assertEquals(null, $url->getChangeFreq());

        $url->setChangeFreq('always');
        $this->assertEquals('always', $url->getChangeFreq());
    }

    public function testPriority()
    {
        $url = new Items\Url('test');
        $url->setDomain('https://example.com');

        $url->setPriority(1);
        $this->assertEquals('1.0', $url->getPriority());

        $url->setPriority(0);
        $this->assertEquals(0.0, $url->getPriority());

        $url->setPriority(.5);
        $this->assertEquals('0.5', $url->getPriority());

        $url->setPriority(2);
        $this->assertEquals(null, $url->getPriority());

        $url->setPriority(-1);
        $this->assertEquals(null, $url->getPriority());
    }

    public function testToArray()
    {
        date_default_timezone_set('Europe/London');

        $url = new Items\Url('test');
        $url->setDomain('https://example.com');
        $url->setLastMod(new DateTime('2013-11-16'));
        $url->setChangeFreq('always');
        $url->setPriority(1);

        $expectedResult = [
            'loc' => 'https://example.com/test',
            'lastmod' => '2013-11-16',
            'changefreq' => 'always',
            'priority' => '1.0'
        ];

        $this->assertEquals($expectedResult, $url->toArray());
    }
}

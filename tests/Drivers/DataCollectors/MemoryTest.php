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

namespace Wszetko\Sitemap\Tests\Drivers\DataCollectors;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Drivers\DataCollectors\Memory;
use Wszetko\Sitemap\Items\Mobile;
use Wszetko\Sitemap\Items\Url;

/**
 * Class MemoryTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class MemoryTest extends TestCase
{
    public function testConstructor()
    {
        $driver = new Memory();
        $this->assertInstanceOf(Memory::class, $driver);
    }

    public function testElement()
    {
        $driver = new Memory();
        $url = new Url('/');
        $driver->add($url, 'test');

        $this->assertCount(1, $driver->fetchAll() ?? []);
        $this->assertCount(1, $driver->fetchGroup('test') ?? []);
        $this->assertEquals(1, $driver->getGroupsCount());
        $this->assertEquals(1, $driver->getGroupCount('test'));
        $this->assertEquals(1, $driver->getCount());
        $this->assertEquals($url->toArray(), $driver->fetch('test'));
        $this->assertTrue($driver->isLast('test'));
        $this->assertNull($driver->fetch('test'));
        $this->assertNull($driver->fetch('bad'));
    }

    public function testExtensions()
    {
        $driver = new Memory();
        $url = new Url('/');
        $url->setDomain('https://example.com');
        $url->addExtension(new Mobile());
        $driver->add($url, 'test');
        $this->assertEquals(['mobile' => Mobile::NAMESPACE_URL], $driver->getExtensions());
    }
}

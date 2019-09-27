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

use Exception;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Drivers\DataCollectors\Memory;
use Wszetko\Sitemap\Drivers\Output\OutputXMLWriter;
use Wszetko\Sitemap\Items\Url;
use Wszetko\Sitemap\Sitemap;

/**
 * Class SitemapTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class SitemapTest extends TestCase
{
    public function testConstructor()
    {
        $sitemap = new Sitemap();
        $this->assertInstanceOf(Sitemap::class, $sitemap);
    }

    public function testDomain()
    {
        $sitemap = new Sitemap('https://example.com');
        $this->assertEquals('https://example.com', $sitemap->getDomain());
    }

    public function testDefaultFilename()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setDefaultFilename('test');
        $this->assertEquals('test', $sitemap->getDefaultFilename());
    }

    public function testDataCollector()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setDataCollector(Memory::class);
        $this->assertInstanceOf(Memory::class, $sitemap->getDataCollector());
    }

    public function testDataCollectorException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BadOne data collector does not exists.');
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setDataCollector('BadOne');
    }

    /**
     * @throws \Exception
     */
    public function testPublicDirectory()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setPublicDirectory(__DIR__);
        $this->assertEquals(__DIR__, $sitemap->getPublicDirectory());
    }

    /**
     * @throws \Exception
     */
    public function testPublicDirectoryException()
    {
        $sitemap = new Sitemap('https://example.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Sitemap directory does not exists.');
        $sitemap->setPublicDirectory(__DIR__ . 'NotExists');
    }

    public function testXMLCase1()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setXml(OutputXMLWriter::class, ['domain' => $sitemap->getDomain()]);
        $this->assertInstanceOf(OutputXMLWriter::class, $sitemap->getXml());
    }

    public function testXMLCase2()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setXml(OutputXMLWriter::class, []);
        $this->assertInstanceOf(OutputXMLWriter::class, $sitemap->getXml());
    }

    public function testTempDirectoryCase1()
    {
        $sitemap = new Sitemap('https://example.com');
        $this->assertStringContainsString(DIRECTORY_SEPARATOR . 'sitemap', $sitemap->getTempDirectory());
    }

    public function testTempDirectoryCase2()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setSitepamsDirectory('sitemaps');
        $this->assertStringContainsString(
            $sitemap->getTempDirectory() . DIRECTORY_SEPARATOR, $sitemap->getSitepamsTempDirectory()
        );
    }

    /**
     * @dataProvider addItemProvider
     *
     * @param mixed $items
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testAddItem($items, $expected)
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setDataCollector(Memory::class);

        foreach ($items as $item) {
            $url = new Url($item);
            $sitemap->addItem($url);
        }

        if ($sitemap->getDataCollector() !== null) {
            $result = $sitemap->getDataCollector()->fetchAll($sitemap->getDefaultFilename());
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @return array
     */
    public function addItemProvider()
    {
        return [
            [['/'], [['url' => ['loc' => 'https://example.com/']]]],
            [
                ['/', '/test'],
                [['url' => ['loc' => 'https://example.com/']], ['url' => ['loc' => 'https://example.com/test']]]
            ],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddItems()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setDataCollector(Memory::class);
        $items = [new Url('/'), new Url('/test')];
        $sitemap->addItems($items);

        if ($sitemap->getDataCollector() !== null) {
            $result = $sitemap->getDataCollector()->fetchAll($sitemap->getDefaultFilename());
            $this->assertEquals([
                ['url' => ['loc' => 'https://example.com/']],
                ['url' => ['loc' => 'https://example.com/test']]
            ], $result);
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddItemException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('DataCollector is not set.');
        $sitemap = new Sitemap('https://example.com');
        $item = new Url('/');
        $sitemap->addItem($item);
    }

    public function testSeparator()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setSeparator('_');
        $this->assertEquals('_', $sitemap->getSeparator());
    }

    public function testUseCompressionTrue()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setUseCompression(true);
        $this->assertTrue($sitemap->isUseCompression());
    }

    public function testUseCompressionFalse()
    {
        $sitemap = new Sitemap('https://example.com');
        $this->assertFalse($sitemap->isUseCompression());
    }

    public function testIndexFilename()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setIndexFilename('test');
        $this->assertEquals('test', $sitemap->getIndexFilename());
    }
}

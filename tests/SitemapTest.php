<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Drivers\DataCollectors\Memory;
use Wszetko\Sitemap\Drivers\XML\XMLWriter;
use Wszetko\Sitemap\Items\Url;
use Wszetko\Sitemap\Sitemap;

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
        $sitemap->setDataCollector('Memory');
        $this->assertInstanceOf(Memory::class, $sitemap->getDataCollector());

        $sitemap = new Sitemap('https://example.com');
        $sitemap->setDataCollector('BadOne');
        $this->assertNull($sitemap->getDataCollector());
    }

    public function testPublicDirectory()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setPublicDirectory(__DIR__);
        $this->assertEquals(__DIR__, $sitemap->getPublicDirectory());

        $sitemap = new Sitemap('https://example.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Sitemap directory does not exists.');
        $sitemap->setPublicDirectory(__DIR__ . 'NotExists');
    }

    public function testXML()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setXml(XMLWriter::class, ['domain' => $sitemap->getDomain()]);
        $this->assertInstanceOf(XMLWriter::class, $sitemap->getXml());

        $sitemap = new Sitemap('https://example.com');
        $sitemap->setXml(XMLWriter::class, []);
        $this->assertInstanceOf(XMLWriter::class, $sitemap->getXml());
    }

    public function testTempDirectory()
    {
        $sitemap = new Sitemap('https://example.com');
        $this->assertStringContainsString(DIRECTORY_SEPARATOR . 'sitemap', $sitemap->getTempDirectory());

        $sitemap->setSitepamsDirectory('sitemaps');
        $this->assertStringContainsString($sitemap->getTempDirectory() . DIRECTORY_SEPARATOR, $sitemap->getSitepamsTempDirectory());
    }

    public function testAddItem()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setDataCollector('Memory');
        $item = new Url('/');
        $sitemap->addItem($item);
        $this->assertEquals(['url' => ['loc' => 'https://example.com/']], $sitemap->getDataCollector()->fetch($sitemap->getDefaultFilename()));
    }

    public function testSeparator()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setSeparator('_');
        $this->assertEquals('_', $sitemap->getSeparator());
    }

    public function testUseCompression()
    {
        $sitemap = new Sitemap('https://example.com');
        $this->assertFalse($sitemap->isUseCompression());
        $sitemap->setUseCompression(true);
        $this->assertTrue($sitemap->isUseCompression());
    }

    public function testIndexFilename()
    {
        $sitemap = new Sitemap('https://example.com');
        $sitemap->setIndexFilename('test');
        $this->assertEquals('test', $sitemap->getIndexFilename());
    }
}

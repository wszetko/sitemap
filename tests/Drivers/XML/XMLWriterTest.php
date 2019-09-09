<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Drivers\XML\XMLWriter;

class XMLWriterTest extends TestCase
{
    public function testConstructor()
    {
        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $this->assertInstanceOf(XMLWriter::class, $driver);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Domain is not set.');
        new XMLWriter([]);
    }

    public function testDomain()
    {
        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $this->assertEquals('https://example.com', $driver->getDomain());

        $driver = new XMLWriter(['domain' => 'https://example.com/']);
        $this->assertEquals('https://example.com', $driver->getDomain());
    }

    public function testWorkDir()
    {
        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $driver->setWorkDir(__DIR__);
        $this->assertEquals(__DIR__, $driver->getWorkDir());
    }
}
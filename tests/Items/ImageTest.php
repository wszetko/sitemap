<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;

/**
 * Class ImageTest
 *
 * @package Wszetko\Sitemap\Tests
 */
class ImageTest extends TestCase
{
    public function testConstructor()
    {
        $image = new Items\Image('image.png');

        $this->assertInstanceOf(Items\Image::class, $image);
    }

    public function testGetLoc()
    {
        $image = new Items\Image('image.png');
        $image->setDomain('https://example.com');

        $this->assertEquals('https://example.com/image.png', $image->getLoc());

        unset($image);

        $image = new Items\Image('/image.png');
        $image->setDomain('https://example.com');

        $this->assertEquals('https://example.com/image.png', $image->getLoc());
    }

    public function testCaption()
    {
        $image = new Items\Image('image.png');
        $image->setCaption('Test Caption');

        $this->assertEquals('Test Caption', $image->getCaption());
    }

    public function testGeolocation()
    {
        $image = new Items\Image('image.png');
        $image->setGeoLocation('Gdynia, Poland');

        $this->assertEquals('Gdynia, Poland', $image->getGeoLocation());
    }

    public function testTitle()
    {
        $image = new Items\Image('image.png');
        $image->setTitle('Title example');

        $this->assertEquals('Title example', $image->getTitle());
    }

    public function testLicense()
    {
        $image = new Items\Image('image.png');
        $image->setLicense('https://example/licence.txt');

        $this->assertEquals('https://example/licence.txt', $image->getLicense());

        $image->setLicense('Invalid Licence');

        $this->assertNull($image->getLicense());
    }

    public function testToArray()
    {
        $image = new Items\Image('image.png');
        $image->setDomain('https://example.com');
        $image->setCaption('Test Caption');
        $image->setGeoLocation('Gdynia, Poland');
        $image->setTitle('Title example');
        $image->setLicense('https://example/licence.txt');

        $expectedResult = [
            '_namespace' => $image::NAMESPACE_NAME,
            '_element' => 'image',
            'image' => [
                'loc' => 'https://example.com/image.png',
                'caption' => 'Test Caption',
                'geo_location' => 'Gdynia, Poland',
                'title' => 'Title example',
                'license' => 'https://example/licence.txt'
            ]
        ];

        $this->assertEquals($expectedResult, $image->toArray());
    }
}
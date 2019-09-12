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

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;

/**
 * Class ImageTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 * @coversNothing
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
        $this->assertEquals('https://example.com/image.png', $image->getLoc(), 'Faild testGetLoc without leading slash.');

        $image = new Items\Image('/image.png');
        $image->setDomain('https://example.com');
        $this->assertEquals('https://example.com/image.png', $image->getLoc(), 'Faild testGetLoc with leading slash.');
    }

    public function testCaption()
    {
        $image = new Items\Image('image.png');
        $image->setCaption('Test Caption');
        $this->assertEquals('Test Caption', $image->getCaption());

        $image = new Items\Image('image.png');
        $image->setCaption(100);
        $this->assertEquals('100', $image->getCaption());

        $image = new Items\Image('image.png');
        $caption = new class() {
            public function __toString()
            {
                return 'Test';
            }
        };
        $image->setCaption($caption);
        $this->assertEquals('Test', $image->getCaption());
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
        $image->setDomain('https://example.com');
        $image->setLicense('/licence.txt');
        $this->assertEquals('https://example.com/licence.txt', $image->getLicense());

        $image = new Items\Image('image.png');
        $image->setLicense('https://creativecommons.org/licenses/by-sa/2.0/');
        $this->assertEquals('https://creativecommons.org/licenses/by-sa/2.0/', $image->getLicense());

        $image = new Items\Image('image.png');
        $image->setDomain('https://example.com');
        $image->setLicense('https://creativecommons.org/licenses/by-sa/2.0/');
        $this->assertEquals('https://creativecommons.org/licenses/by-sa/2.0/', $image->getLicense());
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
                'license' => 'https://example/licence.txt',
            ],
        ];

        $this->assertEquals($expectedResult, $image->toArray());
    }
}

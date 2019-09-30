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

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;

/**
 * Class ImageTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class ImageTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $image = new Items\Image('image.png');

        $this->assertInstanceOf(Items\Image::class, $image);
    }

    /**
     * @dataProvider getLocProvider
     *
     * @throws \ReflectionException
     */
    public function testGetLoc(string $image, string $expected)
    {
        $test = new Items\Image($image);
        $test->setDomain('https://example.com');
        $this->assertEquals($expected, $test->getLoc());
    }

    /**
     * @return array
     */
    public function getLocProvider()
    {
        return [
            ['image.png', 'https://example.com/image.png'],
            ['/image.png', 'https://example.com/image.png'],
            ['path/image.png', 'https://example.com/path/image.png'],
        ];
    }

    /**
     * @dataProvider captionProvider
     *
     * @param mixed  $caption
     * @param string $expected
     *
     * @throws \ReflectionException
     */
    public function testCaption($caption, string $expected)
    {
        $image = new Items\Image('image.png');
        $image->setCaption($caption);
        $this->assertEquals($expected, $image->getCaption());
    }

    /**
     * @return array
     */
    public function captionProvider()
    {
        $in = new class() {
            public function __toString()
            {
                return 'Test';
            }
        };

        return [
            ['Test Caption', 'Test Caption'],
            [100, '100'],
            [$in, 'Test'],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function testGeolocation()
    {
        $image = new Items\Image('image.png');
        $image->setGeoLocation('Gdynia, Poland');

        $this->assertEquals('Gdynia, Poland', $image->getGeoLocation());
    }

    /**
     * @throws \ReflectionException
     */
    public function testTitle()
    {
        $image = new Items\Image('image.png');
        $image->setTitle('Title example');

        $this->assertEquals('Title example', $image->getTitle());
    }

    /**
     * @throws \ReflectionException
     */
    public function testTitleInvalid()
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to undefined method Wszetko\Sitemap\Items\AbstractItem::addTitle()');
        $image = new Items\Image('image.png');
        $wrong = 'addTitle';
        $image->$wrong('Title example');
    }

    /**
     * @dataProvider licenseProvider
     *
     * @param mixed $image
     * @param mixed $domain
     * @param mixed $licence
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testLicense($image, $domain, $licence, $expected)
    {
        $test = new Items\Image($image);
        if (!empty($domain)) {
            $test->setDomain($domain);
        }
        $test->setLicense($licence);
        $this->assertEquals($expected, $test->getLicense());
    }

    /**
     * @return array
     */
    public function licenseProvider()
    {
        return [
            ['image.png', 'https://example.com', '/licence.txt', 'https://example.com/licence.txt'],
            ['image.png', 'https://example.com', 'https://creativecommons.org/licenses/by-sa/2.0/', 'https://creativecommons.org/licenses/by-sa/2.0/'],
            ['image.png', '', 'https://creativecommons.org/licenses/by-sa/2.0/', 'https://creativecommons.org/licenses/by-sa/2.0/'],
        ];
    }

    /**
     * @throws \ReflectionException
     */
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

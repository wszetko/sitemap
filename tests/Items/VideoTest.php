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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items\Video;

/**
 * Class VideoTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 * @coversNothing
 */
class VideoTest extends TestCase
{
    public function testConstructor()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertInstanceOf(Video::class, $video);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('thumbnailLoc need to be set.');
        new Video('', 'Video', 'Description');
    }

    public function testContentLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setContentLoc('example/test');
        $this->assertEquals('https://example.com/example/test', $video->getContentLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setContentLoc('/example/test');
        $this->assertEquals('https://example.com/example/test', $video->getContentLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $this->assertNull($video->getContentLoc());
    }

    public function testPlayerLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setPlayerLoc('/player.swf');
        $this->assertEquals('https://example.com/player.swf', $video->getPlayerLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setPlayerLoc('player.swf');
        $this->assertEquals('https://example.com/player.swf', $video->getPlayerLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setPlayerLoc('/player.swf', 'Yes');
        $this->assertEquals(['https://example.com/player.swf' => ['allow_embed' => 'Yes']], $video->getPlayerLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setPlayerLoc('/player.swf', 'Yes', 'string');
        $this->assertEquals(['https://example.com/player.swf' => ['allow_embed' => 'Yes', 'autoplay' => 'string']], $video->getPlayerLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setPlayerLoc('/player.swf', null, 'string');
        $this->assertEquals(['https://example.com/player.swf' => ['autoplay' => 'string']], $video->getPlayerLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $this->assertNull($video->getPlayerLoc());
    }

    public function testThumbnailLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $this->assertEquals('https://example.com/thumb.png', $video->getThumbnailLoc());

        $video = new Video('/thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $this->assertEquals('https://example.com/thumb.png', $video->getThumbnailLoc());
    }

    public function testGetTitle()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertEquals('Video', $video->getTitle());

        $video = new Video('thumb.png', 'VideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoMOREthan100chars', 'Description');
        $this->assertEquals('VideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideo', $video->getTitle());

        $video = new Video('thumb.png', '', 'Description');
        $this->assertNull($video->getTitle());
    }

    public function testGetDescription()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertEquals('Description', $video->getDescription());

        $video = new Video('thumb.png', 'Video', 'Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one.');
        $this->assertEquals('Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one', $video->getDescription());
    }

    public function testDuration()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getDuration());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDuration(60);
        $this->assertEquals('60', $video->getDuration());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDuration(-10);
        $this->assertNull($video->getDuration());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDuration(30000);
        $this->assertNull($video->getDuration());
    }

    public function testExpirationDate()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getExpirationDate());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setExpirationDate('2020-01-01');
        $this->assertEquals('2020-01-01', $video->getExpirationDate());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setExpirationDate(new \DateTime('2020-01-01'));
        $this->assertEquals('2020-01-01', $video->getExpirationDate());
    }

    public function testRating()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(null);
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(0);
        $this->assertEquals('0.0', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(5);
        $this->assertEquals('5.0', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(2.5);
        $this->assertEquals('2.5', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(2.333);
        $this->assertEquals('2.3', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(2.666);
        $this->assertEquals('2.7', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(10);
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(-1);
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('0');
        $this->assertEquals('0.0', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('5');
        $this->assertEquals('5.0', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('2.5');
        $this->assertEquals('2.5', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('2.333');
        $this->assertEquals('2.3', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('2.666');
        $this->assertEquals('2.7', $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('10');
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('-1');
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('Error');
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating('');
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(new \stdClass());
        $this->assertNull($video->getRating());
    }

    public function testViewCount()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getViewCount());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount(10);
        $this->assertEquals('10', $video->getViewCount());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount('10');
        $this->assertEquals('10', $video->getViewCount());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount(10.1);
        $this->assertEquals('10', $video->getViewCount());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount('10.1');
        $this->assertEquals('10', $video->getViewCount());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount('');
        $this->assertNull($video->getViewCount());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount('Bad');
        $this->assertNull($video->getViewCount());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount(new \stdClass());
        $this->assertNull($video->getViewCount());
    }

    public function testPublicationDate()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getPublicationDate());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPublicationDate('2010-01-01');
        $this->assertEquals('2010-01-01', $video->getPublicationDate());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPublicationDate(new \DateTime('2010-01-01'));
        $this->assertEquals('2010-01-01', $video->getPublicationDate());
    }

    public function testFamilyFriendly()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getFamilyFriendly());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setFamilyFriendly(true);
        $this->assertEquals('Yes', $video->getFamilyFriendly());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setFamilyFriendly(false);
        $this->assertEquals('No', $video->getFamilyFriendly());
    }

    public function testRestriction()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getRestriction());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRestriction('GB DE', 'allow');
        $this->assertEquals(['GB DE' => ['relationship' => 'allow']], $video->getRestriction());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRestriction('US', 'deny');
        $this->assertEquals(['US' => ['relationship' => 'deny']], $video->getRestriction());
    }

    public function testPlatform()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getPlatform());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPlatform('web', 'allow');
        $this->assertEquals(['web' => ['relationship' => 'allow']], $video->getPlatform());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPlatform('mobile', 'deny');
        $this->assertEquals(['mobile' => ['relationship' => 'deny']], $video->getPlatform());
    }

    public function testPrice()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getPrice());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPrice(9.99, 'USD');
        $this->assertEquals(['9.99' => ['currency' => 'USD']], $video->getPrice());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPrice(10, 'USD', 'rent', 'sd');
        $this->assertEquals(['10.00' => ['currency' => 'USD', 'type' => 'rent', 'resolution' => 'SD']], $video->getPrice());
    }

    public function testRequireSubscription()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getRequiresSubscription());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRequiresSubscription(true);
        $this->assertEquals('Yes', $video->getRequiresSubscription());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRequiresSubscription(false);
        $this->assertEquals('No', $video->getRequiresSubscription());
    }

    public function testUploader()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getUploader());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setUploader('UserName');
        $this->assertEquals('UserName', $video->getUploader());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setUploader('UserName', '/username');
        $this->assertEquals(['UserName' => ['info' => 'https://example.com/username']], $video->getUploader());
    }

    public function testLive()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getLive());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setLive(true);
        $this->assertEquals('Yes', $video->getLive());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setLive(false);
        $this->assertEquals('No', $video->getLive());
    }

    public function testTags()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getTag());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setTag(['tag']);
        $this->assertEquals(['tag'], $video->getTag());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setTag('tag');
        $this->assertEquals(['tag'], $video->getTag());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->addTag('tag');
        $this->assertEquals(['tag'], $video->getTag());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setTag(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33']);
        $this->assertEquals(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32'], $video->getTag());
    }

    public function testCategory()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getCategory());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setCategory('Travel');
        $this->assertEquals('Travel', $video->getCategory());
    }

    public function testGalleryLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getGalleryLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setGalleryLoc('https://example.com/gallery');
        $this->assertEquals('https://example.com/gallery', $video->getGalleryLoc());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setGalleryLoc('/gallery');
        $this->assertEquals('https://example.com/gallery', $video->getGalleryLoc());
    }

    public function testInvalidDomainOnContentLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getContentLoc());
    }

    public function testInvalidDomainOnPlayerLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getPlayerLoc());
    }

    public function testToArray()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setContentLoc('example/test');
        $video->setPlayerLoc('/player.swf', 'Yes', 'autoplay');
        $video->setDuration(60);
        $video->setExpirationDate('2020-01-01');
        $video->setPublicationDate('2018-01-01');
        $video->setRating(5);
        $video->setViewCount(10);
        $video->setFamilyFriendly(true);
        $video->setRestriction('GB DE', 'allow');
        $video->setPlatform('web', 'allow');
        $video->setPrice(10, 'USD', 'rent', 'sd');
        $video->setRequiresSubscription(false);
        $video->setUploader('UserName', '/username');
        $video->setLive(false);
        $video->setTag(['tag']);
        $video->setCategory('Travel');
        $video->setGalleryLoc('https://example.com/gallery');

        $this->assertEquals([
            '_namespace' => 'video',
            '_element' => 'video',
            'video' => [
                'thumbnail_loc' => 'https://example.com/thumb.png',
                'title' => 'Video',
                'description' => 'Description',
                'content_loc' => 'https://example.com/example/test',
                'player_loc' => [
                    '_attributes' => [
                        'allow_embed' => 'Yes',
                        'autoplay' => 'autoplay',
                    ],
                    '_value' => 'https://example.com/player.swf',
                ],
                'duration' => '60',
                'expiration_date' => '2020-01-01',
                'rating' => '5.0',
                'view_count' => '10',
                'publication_date' => '2018-01-01',
                'family_friendly' => 'Yes',
                'restriction' => [
                    '_attributes' => ['relationship' => 'allow'],
                    '_value' => 'GB DE',
                ],
                'platform' => [
                    '_attributes' => ['relationship' => 'allow'],
                    '_value' => 'web',
                ],
                'price' => [
                    '_attributes' => [
                        'currency' => 'USD',
                        'type' => 'rent',
                        'resolution' => 'SD',
                    ],
                    '_value' => '10.00',
                ],
                'requires_subscription' => 'No',
                'uploader' => [
                    '_attributes' => [
                        'info' => 'https://example.com/username',
                    ],
                    '_value' => 'UserName',
                ],
                'live' => 'No',
                'tag' => ['tag'],
                'category' => 'Travel',
                'gallery_loc' => 'https://example.com/gallery',
            ],
        ], $video->toArray());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setPlayerLoc('/player.swf');
        $video->setUploader('UserName');

        $this->assertEquals([
            '_namespace' => 'video',
            '_element' => 'video',
            'video' => [
                'thumbnail_loc' => 'https://example.com/thumb.png',
                'title' => 'Video',
                'description' => 'Description',
                'player_loc' => 'https://example.com/player.swf',
                'uploader' => 'UserName',
            ],
        ], $video->toArray());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nor content_loc or player_loc parameter is set.');
        $video->toArray();
    }
}

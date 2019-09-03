<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items\Video;

/**
 * Class VideoTest
 *
 * @package Wszetko\Sitemap\Tests
 */
class VideoTest extends TestCase
{
    public function testConstructor()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertInstanceOf(Video::class, $video);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid thumbnail location parameter.');
        new Video('|^bad', 'Video', 'Description');
    }

    public function testCurrency()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setCurrency('PLN');

        $this->assertEquals('PLN', $video->getCurrency());
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
        $this->assertEquals(['https://example.com/player.swf' => 'Yes'], $video->getPlayerLoc());
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
    }

    public function testGetDescription()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertEquals('Description', $video->getDescription());
    }

    public function testDuration()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getDuration());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDuration(60);
        $this->assertEquals(60, $video->getDuration());

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
        $video->setRating(0);
        $this->assertEquals(0, $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(5);
        $this->assertEquals(5, $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(2.5);
        $this->assertEquals(2.5, $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(2.333);
        $this->assertEquals(2.3, $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(2.666);
        $this->assertEquals(2.7, $video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(10);
        $this->assertNull($video->getRating());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating(-1);
        $this->assertNull($video->getRating());
    }

    public function testViewCount()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getViewCount());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount(10);
        $this->assertEquals(10, $video->getViewCount());
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
        $video->setRestriction('allow', 'GB DE');
        $this->assertEquals(['allow' => 'GB DE'], $video->getRestriction());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRestriction('deny', 'US');
        $this->assertEquals(['deny' => 'US'], $video->getRestriction());
    }

    public function testPlatform()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getPlatform());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPlatform('allow', 'web');
        $this->assertEquals(['allow' => 'web'], $video->getPlatform());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPlatform('deny', 'mobile');
        $this->assertEquals(['deny' => 'mobile'], $video->getPlatform());
    }

    public function testPrice()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getPrice());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPrice(10, 'USD', 'rent', 'sd');
        $this->assertEquals(['price' => 10, 'currency' => 'USD', 'type' => 'rent', 'resolution' => 'sd'], $video->getPrice());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPrice(10, 'USD');
        $this->assertEquals(['price' => 10, 'currency' => 'USD'], $video->getPrice());
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
        $this->assertEquals(['UserName' => 'https://example.com/username'], $video->getUploader());
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
        $this->assertNull($video->getTags());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setTags(['tag']);
        $this->assertEquals(['tag'], $video->getTags());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setTags(['1' ,'2', '3', '4', '5', '6', '7', '8', '9', '10', '11' ,'12', '13', '14', '15', '16', '17', '18', '19', '20','21' ,'22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33']);
        $this->assertEquals(['1' ,'2', '3', '4', '5', '6', '7', '8', '9', '10', '11' ,'12', '13', '14', '15', '16', '17', '18', '19', '20','21' ,'22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32'], $video->getTags());
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
        $video->setGalleryLoc('https://example.com/gallery');
        $this->assertEquals('https://example.com/gallery', $video->getGalleryLoc());
    }

    public function testToArray()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setContentLoc('example/test');
        $video->setPlayerLoc('/player.swf', 'Yes');
        $video->setDuration(60);
        $video->setExpirationDate('2020-01-01');
        $video->setPublicationDate('2018-01-01');
        $video->setRating(5);
        $video->setViewCount(10);
        $video->setFamilyFriendly(true);
        $video->setRestriction('allow', 'GB DE');
        $video->setPlatform('allow', 'web');
        $video->setPrice(10, 'USD', 'rent', 'sd');
        $video->setRequiresSubscription(false);
        $video->setUploader('UserName', '/username');
        $video->setLive(false);
        $video->setTags(['tag']);
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
                    '_attributes' => ['allow_embed' => 'Yes'],
                    '_value' => 'https://example.com/player.swf'
                ],
                'duration' => 60,
                'expiration_date' => '2020-01-01',
                'rating' => 5.0,
                'view_count' => 10,
                'publication_date' => '2018-01-01',
                'family_friendly' => 'Yes',
                'restriction' => [
                    '_attributes' => ['relationship' => 'allow'],
                    '_value' => 'GB DE'
                ],
                'platform' => [
                    '_attributes' => ['relationship' => 'allow'],
                    '_value' => 'web'
                ],
                'price' => [
                    '_attributes' => [
                        'currency' => 'USD',
                        'type' => 'rent',
                        'resolution' => 'sd'
                    ],
                    '_value' => 10
                ],
                'requires_subscription' => 'No',
                'uploader' => [
                    '_attributes' => [
                        'info' => 'https://example.com/username'
                    ],
                    '_value' => 'UserName'
                ],
                'live' => 'No',
                'tag' => ['tag'],
                'category' => 'Travel',
                'gallery_loc' => 'https://example.com/gallery'
            ]
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
                'uploader' => 'UserName'
            ]
        ], $video->toArray());

        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nor content_loc or player_loc parameter is set.');
        $video->toArray();
    }

    public function testInvalidDomainOnContentLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Domain is not set.');
        $video->getContentLoc();
    }

    public function testInvalidDomainOnPlayerLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Domain is not set.');
        $video->getPlayerLoc();
    }
}
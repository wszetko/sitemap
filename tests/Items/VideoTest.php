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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items\Video;

/**
 * Class VideoTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class VideoTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertInstanceOf(Video::class, $video);
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorExceptionCase1()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('title need to be set.');
        new Video('thumb.png', '', 'Description');
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorExceptionCase2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('description need to be set.');
        new Video('thumb.png', 'Video', '');
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorExceptionCase3()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('thumbnailLoc need to be set.');
        new Video('', 'Video', 'Description');
    }

    /**
     * @dataProvider contentLocProvider
     *
     * @param mixed $contentLoc
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testContentLoc($contentLoc, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');

        if (!empty($contentLoc)) {
            $video->setContentLoc($contentLoc);
        }

        $this->assertEquals($expected, $video->getContentLoc());
    }

    /**
     * @return array
     */
    public function contentLocProvider()
    {
        return [
            ['example/test', 'https://example.com/example/test'],
            ['/example/test', 'https://example.com/example/test'],
            ['', null],
        ];
    }

    /**
     * @dataProvider playerLocProvider
     *
     * @param mixed $player
     * @param mixed $allow_embed
     * @param mixed $autoplay
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testPlayerLoc($player, $allow_embed, $autoplay, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setPlayerLoc($player, $allow_embed, $autoplay);
        $this->assertEquals($expected, $video->getPlayerLoc());
    }

    /**
     * @return array
     */
    public function playerLocProvider()
    {
        return [
            ['/player.swf', null, null, 'https://example.com/player.swf'],
            ['player.swf', null, null, 'https://example.com/player.swf'],
            ['/player.swf', 'yes', null, ['https://example.com/player.swf' => ['allow_embed' => 'Yes']]],
            ['/player.swf', 'yes', 'string', ['https://example.com/player.swf' => ['allow_embed' => 'Yes', 'autoplay' => 'string']]],
            ['/player.swf', null, 'string', ['https://example.com/player.swf' => ['autoplay' => 'string']]],
            [null, null, null, null],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function testInvalidDomainOnPlayerLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getPlayerLoc());
    }

    /**
     * @dataProvider getThumbnailLocProvider
     *
     * @param mixed $thumbnailLoc
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testGetThumbnailLoc($thumbnailLoc, $expected)
    {
        $video = new Video($thumbnailLoc, 'Video', 'Description');
        $video->setDomain('https://example.com');
        $this->assertEquals($expected, $video->getThumbnailLoc());
    }

    /**
     * @return array
     */
    public function getThumbnailLocProvider()
    {
        return [
            ['thumb.png', 'https://example.com/thumb.png'],
            ['/thumb.png', 'https://example.com/thumb.png'],
        ];
    }

    /**
     * @dataProvider getTitleProvider
     *
     * @param mixed $title
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testGetTitle($title, $expected)
    {
        $video = new Video('thumb.png', $title, 'Description');
        $this->assertEquals($expected, $video->getTitle());
    }

    /**
     * @return array
     */
    public function getTitleProvider()
    {
        return [
            ['Video', 'Video'],
            ['VideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoMOREthan100chars', 'VideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideoVideo'],
        ];
    }

    /**
     * @dataProvider getDescriptionPrivider
     *
     * @param mixed $description
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testGetDescription($description, $expected)
    {
        $video = new Video('thumb.png', 'Video', $description);
        $this->assertEquals($expected, $video->getDescription());
    }

    /**
     * @return array
     */
    public function getDescriptionPrivider()
    {
        return [
            ['Description', 'Description'],
            ['Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one.', 'Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one. Long description test for to trim to correct one'],
        ];
    }

    /**
     * @dataProvider durationProvider
     *
     * @param mixed $duration
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testDuration($duration, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDuration($duration);
        $this->assertEquals($expected, $video->getDuration());
    }

    /**
     * @return array
     */
    public function durationProvider()
    {
        return [
            [60, '60'],
            ['60', '60'],
            [-10, null],
            ['-10', null],
            [30000, null],
            ['30000', null],
            [10.1, '10'],
            ['10.1', '10'],
            [null, null],
        ];
    }

    /**
     * @dataProvider expirationDateProvider
     *
     * @param mixed $expirationDate
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testExpirationDate($expirationDate, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setExpirationDate($expirationDate);
        $this->assertEquals($expected, $video->getExpirationDate());
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function expirationDateProvider()
    {
        return [
            [null, null],
            ['2020-01-01', '2020-01-01'],
            [new \DateTime('2020-01-01'), '2020-01-01'],
        ];
    }

    /**
     * @dataProvider ratingProvider
     *
     * @param mixed $rating
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testRating($rating, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRating($rating);
        $this->assertEquals($expected, $video->getRating());
    }

    /**
     * @return array
     */
    public function ratingProvider()
    {
        return [
            [null, null],
            [0, '0.0'],
            [5, '5.0'],
            [2.5, '2.5'],
            [2.333, '2.3'],
            [2.666, '2.7'],
            [10, null],
            [-1, null],
            ['0', '0.0'],
            ['5', '5.0'],
            ['2.5', '2.5'],
            ['2.333', '2.3'],
            ['2.666', '2.7'],
            ['10', null],
            ['-1', null],
            ['Error', null],
            ['', null],
            [new \stdClass(), null],
        ];
    }

    /**
     * @dataProvider viewCountProvider
     *
     * @param mixed $viewCount
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testViewCount($viewCount, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setViewCount($viewCount);
        $this->assertEquals($expected, $video->getViewCount());
    }

    /**
     * @return array
     */
    public function viewCountProvider()
    {
        return [
            [null, null],
            [10, '10'],
            ['10', '10'],
            [10.1, '10'],
            ['10.1', '10'],
            ['', null],
            ['Bad', null],
            [new \stdClass(), null],
        ];
    }

    /**
     * @dataProvider publicationDateProvider
     *
     * @param mixed $publicationDate
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testPublicationDate($publicationDate, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPublicationDate($publicationDate);
        $this->assertEquals($expected, $video->getPublicationDate());
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function publicationDateProvider()
    {
        return [
            [null, null],
            ['2010-01-01', '2010-01-01'],
            [new \DateTime('2010-01-01'), '2010-01-01'],
        ];
    }

    /**
     * @dataProvider yesNoProvider
     *
     * @param mixed $familyFriendly
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testFamilyFriendly($familyFriendly, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setFamilyFriendly($familyFriendly);
        $this->assertEquals($expected, $video->getFamilyFriendly());
    }

    /**
     * @return array
     */
    public function yesNoProvider()
    {
        return [
            [null, null],
            [true, 'Yes'],
            [false, 'No'],
            ['Yes', 'Yes'],
            ['yes', 'Yes'],
            ['y', 'Yes'],
            ['Y', 'Yes'],
            ['No', 'No'],
            ['no', 'No'],
            ['n', 'No'],
            ['N', 'No'],
            ['1', 'Yes'],
            ['0', 'No'],
            [1, 'Yes'],
            [0, 'No'],
        ];
    }

    /**
     * @dataProvider restrictionProvider
     *
     * @param mixed $country
     * @param mixed $relationship
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testRestriction($country, $relationship, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRestriction($country, $relationship);
        $this->assertEquals($expected, $video->getRestriction());
    }

    /**
     * @return array
     */
    public function restrictionProvider()
    {
        return [
            ['GB DE', 'allow', ['GB DE' => ['relationship' => 'allow']]],
            ['US', 'deny', ['US' => ['relationship' => 'deny']]],
            ['US', 'Deny', ['US' => ['relationship' => 'deny']]],
            ['US', 'DENY', ['US' => ['relationship' => 'deny']]],
            ['us', 'deny', ['US' => ['relationship' => 'deny']]],
            [null, null, null],
        ];
    }

    /**
     * @dataProvider platformProvider
     *
     * @param mixed $platform
     * @param mixed $relationship
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testPlatform($platform, $relationship, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPlatform($platform, $relationship);
        $this->assertEquals($expected, $video->getPlatform());
    }

    /**
     * @return array
     */
    public function platformProvider()
    {
        return [
            ['web', 'allow', ['web' => ['relationship' => 'allow']]],
            ['WEB', 'allow', ['web' => ['relationship' => 'allow']]],
            ['web', 'ALLOW', ['web' => ['relationship' => 'allow']]],
            ['mobile', 'deny', ['mobile' => ['relationship' => 'deny']]],
            [null, null, null],
        ];
    }

    /**
     * @dataProvider priceProvider
     *
     * @param mixed $price
     * @param mixed $currency
     * @param mixed $type
     * @param mixed $resolution
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testPrice($price, $currency, $type, $resolution, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setPrice($price, $currency, $type, $resolution);
        $this->assertEquals($expected, $video->getPrice());
    }

    /**
     * @return array
     */
    public function priceProvider()
    {
        return [
            [null, null, null, null, null],
            [10, 'USD', '', '', ['10.00' => ['currency' => 'USD']]],
            [10, '', '', '', null],
            ['10', 'USD', '', '', ['10.00' => ['currency' => 'USD']]],
            ['10.00', 'USD', '', '', ['10.00' => ['currency' => 'USD']]],
            [10.00, 'USD', '', '', ['10.00' => ['currency' => 'USD']]],
            [10, 'usd', '', '', ['10.00' => ['currency' => 'USD']]],
            [10, 'USD', 'rent', 'SD', ['10.00' => ['currency' => 'USD', 'type' => 'rent', 'resolution' => 'SD']]],
            [10, 'USD', 'rent', '', ['10.00' => ['currency' => 'USD', 'type' => 'rent']]],
            [10, 'USD', 'Rent', '', ['10.00' => ['currency' => 'USD', 'type' => 'rent']]],
            [10, 'USD', 'RENT', '', ['10.00' => ['currency' => 'USD', 'type' => 'rent']]],
            [10, 'USD', '', 'SD', ['10.00' => ['currency' => 'USD', 'resolution' => 'SD']]],
            [10, 'USD', '', 'sd', ['10.00' => ['currency' => 'USD', 'resolution' => 'SD']]],
        ];
    }

    /**
     * @dataProvider yesNoProvider
     *
     * @param mixed $requireSubscription
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testRequireSubscription($requireSubscription, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setRequiresSubscription($requireSubscription);
        $this->assertEquals($expected, $video->getRequiresSubscription());
    }

    /**
     * @dataProvider uploaderProvider
     *
     * @param mixed $uploader
     * @param mixed $info
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testUploader($uploader, $info, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setUploader($uploader, $info);
        $this->assertEquals($expected, $video->getUploader());
    }

    /**
     * @return array
     */
    public function uploaderProvider()
    {
        return [
            [null, null, null],
            ['UserName', null, 'UserName'],
            ['UserName', '/username', ['UserName' => ['info' => 'https://example.com/username']]],
            ['UserName', 'username', ['UserName' => ['info' => 'https://example.com/username']]],
            ['UserName', 'https://example.com/username', ['UserName' => ['info' => 'https://example.com/username']]],
        ];
    }

    /**
     * @dataProvider yesNoProvider
     *
     * @param mixed $live
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testLive($live, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setLive($live);
        $this->assertEquals($expected, $video->getLive());
    }

    /**
     * @dataProvider tagProvider
     *
     * @param mixed $tag
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testTagsCase1($tag, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setTag($tag);
        $this->assertEquals($expected, $video->getTag());
    }

    /**
     * @dataProvider tagProvider
     *
     * @param mixed $tag
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testTagsCase2($tag, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->addTag($tag);
        $this->assertEquals($expected, $video->getTag());
    }

    /**
     * @throws \ReflectionException
     */
    public function testTagsCase3()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->addTag('tag1');
        $video->addTag('tag2');
        $this->assertEquals(['tag1', 'tag2'], $video->getTag());
    }

    /**
     * @return array
     */
    public function tagProvider()
    {
        return [
            ['tag', ['tag']],
            ['tag, tag', ['tag, tag']],
            [['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33'], ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32']],
        ];
    }

    /**
     * @dataProvider categoryProvider
     *
     * @param mixed $category
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testCategory($category, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setCategory($category);
        $this->assertEquals($expected, $video->getCategory());
    }

    /**
     * @return array
     */
    public function categoryProvider()
    {
        return [
            [null, null],
            ['Travel', 'Travel'],
            ['Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at dui volutpat, pretium est vitae, facilisis ex. Ut euismod justo bibendum, imperdiet odio sit amet, faucibus mauris. Fusce non gravida lorem. Nam sit amet tellus lorem. Cras non est orci aliquam.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at dui volutpat, pretium est vitae, facilisis ex. Ut euismod justo bibendum, imperdiet odio sit amet, faucibus mauris. Fusce non gravida lorem. Nam sit amet tellus lorem. Cras non est orci aliq'],
        ];
    }

    /**
     * @dataProvider galleryLocProvider
     *
     * @param mixed $galleryLoc
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testGalleryLoc($galleryLoc, $expected)
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $video->setDomain('https://example.com');
        $video->setGalleryLoc($galleryLoc);
        $this->assertEquals($expected, $video->getGalleryLoc());
    }

    /**
     * @throws \ReflectionException
     */
    public function testInvalidDomainOnContentLoc()
    {
        $video = new Video('thumb.png', 'Video', 'Description');
        $this->assertNull($video->getContentLoc());
    }

    /**
     * @return array
     */
    public function galleryLocProvider()
    {
        return [
            [null, null],
            ['https://example.com/gallery', 'https://example.com/gallery'],
            ['/gallery', 'https://example.com/gallery'],
        ];
    }

    /**
     * @throws \ReflectionException
     */
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

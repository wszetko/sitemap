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

// Simple example how to use this library

// Load Composer autoloader
use Wszetko\Sitemap\Drivers\DataCollectors\Memory;

require_once '../vendor/autoload.php';

try {
    // Create Sitemap object
    $sitemap = new Wszetko\Sitemap\Sitemap();
    $sitemap->setDomain('https://example.com');

    // If directory for sitemap do not exists, create it
    if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'sitemap')) {
        mkdir(__DIR__ . DIRECTORY_SEPARATOR . 'sitemap');
    }

    // Set up Sitemap configuration
    $sitemap->setPublicDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'sitemap');
    $sitemap->setSitemapsDirectory('sitemaps');
    $sitemap->setDataCollector(Memory::class);
    $sitemap->setUseCompression(false);

    // Create example item
    $item = new Wszetko\Sitemap\Items\Url('example-url');
    $item->setLastmod(new DateTime('now'))
        ->setPriority(1)
        ->setChangefreq('never')
    ;

    // Add Mobile extension
    $item->addExtension(new Wszetko\Sitemap\Items\Mobile());

    // Add Image extension
    $image = new Wszetko\Sitemap\Items\Image('/image.png');
    $image->setCaption('Caption for PNG')
        ->setLicense('https://example.com/licence')
        ->setGeoLocation('Gdynia')
        ->setTitle('Title')
    ;
    $item->addExtension($image);

    // Add another Image extension
    $image = new Wszetko\Sitemap\Items\Image('/image.jpg');
    $image->setCaption('Caption for JPG');
    $item->addExtension($image);

    // Add HrefLang extension
    $hrefLang = new Wszetko\Sitemap\Items\HrefLang('pl-PL', '/example-url/pl');
    $hrefLang->addHrefLang('en', '/example-url/en');
    $item->addExtension($hrefLang);

    // Add News extension
    $news = (new Wszetko\Sitemap\Items\News('Test', 'pl', new DateTime('now'), 'Test'))
        ->setAccess('Subscription')
        ->addGenres('PressRelease, Blog')
        ->addKeywords('Keyword1, keyword2')
        ->addStockTickers('NASDAQ:AMAT')
    ;
    $item->addExtension($news);

    // Add Video extension
    $video = (new Wszetko\Sitemap\Items\Video('/thumb.png', 'Video title', 'Video desc'))
        ->setContentLoc('/video.avi')
        ->setContentLoc('/video.mp4')
        ->setPlayerLoc('player.swf', 'Yes')
        ->setPrice(10, 'PLN', 'rent', 'hd')
        ->setDuration(10)
        ->setExpirationDate(new DateTime('now'))
        ->setRating(4.5)
        ->setViewCount(10)
        ->setPublicationDate(new DateTime('now'))
        ->setFamilyFriendly(true)
        ->setRestriction('allow', 'PL US')
        ->setPlatform('allow', 'web')
        ->setRequiresSubscription(false)
        ->setUploader('Uploader', '/uploader-url')
        ->setLive(false)
        ->setTag(['tag1', 'tag2'])
        ->setCategory('Category')
        ->setGalleryLoc('/upload-gallery')
    ;
    $item->addExtension($video);

    // Add Item to Sitemap
    $sitemap->addItem($item);

    // Generate Sitemap
    $sitemap->generate();
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    echo $e->getFile() . ' : ' . $e->getLine() . PHP_EOL;
}

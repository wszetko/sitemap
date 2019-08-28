<?php

/*
 * Simple... No... VERY simple example of usage
 */

// Load Composer autoloader
require_once '../vendor/autoload.php';

try {
    $a = new Wszetko\Sitemap\Sitemap();
    $a->setDomain('https://example.com');
    $a->setPublicDirectory(__DIR__);
    $a->setSitepamsDirectory('sitemaps');
    $a->setDataCollector("Memory");
    $a->setUseCompression(false);
    $item = new Wszetko\Sitemap\Items\Url("example-url");
    $item->addExtension(new Wszetko\Sitemap\Items\Mobile());
    $image = new Wszetko\Sitemap\Items\Image('/image.png');
    $image->setCaption('Caption')
        ->setLicense('https://example.com/licence')
        ->setGeoLocation('Gdynia')
        ->setTitle('Title');
    $item->addExtension($image);
    $item->addExtension(new Wszetko\Sitemap\Items\HrefLang('pl-PL', '/example-url/pl'));
    $news = (new Wszetko\Sitemap\Items\News('Test', 'pl', new DateTime('now'), 'Test'))
        ->setAccess('Subscription')
        ->setGenres('PressRelease, Blog')
        ->setKeywords('Keyword1, keyword2')
        ->setStockTickers('NASDAQ:AMAT');
    $item->addExtension($news);
    $video = (new Wszetko\Sitemap\Items\Video('/thumb.png', 'Video title', 'Video desc'))
        ->setContentLoc('/video.avi')
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
        ->setTags(['tag1', 'tag2'])
        ->setCategory('Category')
        ->setGalleryLoc('/upload-gallery');
    $item->addExtension($video);
    $item->setLastMod(new DateTime('now'))
        ->setPriority('1')
        ->setChangeFreq('never');
    $a->addItem($item);
    $a->generate();
} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL;
    echo $e->getFile()." : ".$e->getLine().PHP_EOL;
}

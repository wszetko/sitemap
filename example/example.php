<?php

/*
 * Simple... No... VERY simple example of usage
 */

// Load Composer autoloader
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
    $sitemap->setSitepamsDirectory('sitemaps');
    $sitemap->setDataCollector("Memory");
    $sitemap->setUseCompression(false);

    // Create example item
    $item = new Wszetko\Sitemap\Items\Url("example-url");
    $item->setLastMod(new DateTime('now'))
        ->setPriority(1)
        ->setChangeFreq('never');

    // Add Mobile extension
    $item->addExtension(new Wszetko\Sitemap\Items\Mobile());

    // Add Image extension
    $image = new Wszetko\Sitemap\Items\Image('/image.png');
    $image->setCaption('Caption')
        ->setLicense('https://example.com/licence')
        ->setGeoLocation('Gdynia')
        ->setTitle('Title');
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
        ->addStockTickers('NASDAQ:AMAT');
    $item->addExtension($news);

    // Add Video extension
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

    // Add Item to Sitemap
    $sitemap->addItem($item);

    // Generate Sitemap
    $sitemap->generate();
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    echo $e->getFile() . " : " . $e->getLine() . PHP_EOL;
}

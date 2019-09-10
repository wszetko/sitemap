<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Drivers\XML\XMLWriter;
use Wszetko\Sitemap\Items\Mobile;

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

    public function testCurrentSitemap()
    {
        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $driver->setCurrentSitemap('test.xml');
        $this->assertEquals('test.xml', $driver->getCurrentSitemap());
    }

    public function testSitemapIndex()
    {
        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest' . date("Y-m-d_H_i_s");
        mkdir($dir);
        $driver->setWorkDir($dir);
        $driver->openSitemapIndex('test.xml');
        $driver->addSitemap('sitemap.xml');
        $driver->closeSitemapIndex();
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
 <sitemap>
  <loc>sitemap.xml</loc>
 </sitemap>
</sitemapindex>", file_get_contents($dir . DIRECTORY_SEPARATOR . 'test.xml'));
        unlink($dir . DIRECTORY_SEPARATOR . 'test.xml');

        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $driver->setWorkDir($dir);
        $driver->openSitemapIndex('test.xml');
        $driver->addSitemap('sitemap.xml', '2019-02-20');
        $driver->closeSitemapIndex();
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
 <sitemap>
  <loc>sitemap.xml</loc>
  <lastmod>2019-02-20</lastmod>
 </sitemap>
</sitemapindex>", file_get_contents($dir . DIRECTORY_SEPARATOR . 'test.xml'));
        unlink($dir . DIRECTORY_SEPARATOR . 'test.xml');
        rmdir($dir);
    }

    public function testSitemap()
    {
        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest' . date("Y-m-d_H_i_s");
        mkdir($dir);
        $driver->setWorkDir($dir);
        $driver->openSitemap('sitemap.xml');
        $driver->closeSitemap();
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"/>", file_get_contents($dir . DIRECTORY_SEPARATOR . 'sitemap.xml'));
        unlink($dir . DIRECTORY_SEPARATOR . 'sitemap.xml');

        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest' . date("Y-m-d_H_i_s");
        $driver->setWorkDir($dir);
        $extensions = ['mobile' => Mobile::NAMESPACE_URL];
        $driver->openSitemap('sitemap.xml', $extensions);
        $driver->closeSitemap();

        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"".Mobile::NAMESPACE_URL."\"/>", file_get_contents($dir . DIRECTORY_SEPARATOR . 'sitemap.xml'));
        unlink($dir . DIRECTORY_SEPARATOR . 'sitemap.xml');
        rmdir($dir);
    }

    public function testUrl()
    {
        $driver = new XMLWriter(['domain' => 'https://example.com']);
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest' . date("Y-m-d_H_i_s");
        mkdir($dir);
        $driver->setWorkDir($dir);
        $driver->openSitemap('sitemap.xml');
        $item = new \Wszetko\Sitemap\Items\Url("example-url");
        $item->setDomain('https://example.com');
        $image = new \Wszetko\Sitemap\Items\Image('/image.png');
        $image->setCaption('Caption for PNG')
            ->setLicense('https://example.com/licence')
            ->setGeoLocation('Gdynia')
            ->setTitle('Title');
        $item->addExtension($image);
        $video = (new \Wszetko\Sitemap\Items\Video('/thumb.png', 'Video title', 'Video desc'))
            ->setContentLoc('/video.avi')
            ->setContentLoc('/video.mp4')
            ->setPlayerLoc('player.swf', 'Yes')
            ->setPrice(10, 'PLN', 'rent', 'hd')
            ->setDuration(10)
            ->setRating(4.5)
            ->setViewCount(10)
            ->setPublicationDate('2019-02-20')
            ->setFamilyFriendly(true)
            ->setRestriction('allow', 'PL US')
            ->setPlatform('allow', 'web')
            ->setRequiresSubscription(false)
            ->setUploader('Uploader', '/uploader-url')
            ->setLive(false)
            ->setTag(['tag1', 'tag2'])
            ->setCategory('Category')
            ->setGalleryLoc('/upload-gallery');
        $item->addExtension($video);
        $driver->addUrl($item->toArray());
        $driver->closeSitemap();
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
 <url>
  <loc>https://example.com/example-url</loc>
  <image:image>
   <image:loc>https://example.com/image.png</image:loc>
   <image:caption>Caption for PNG</image:caption>
   <image:geo_location>Gdynia</image:geo_location>
   <image:title>Title</image:title>
   <image:license>https://example.com/licence</image:license>
  </image:image>
  <video:video>
   <video:thumbnail_loc>https://example.com/thumb.png</video:thumbnail_loc>
   <video:title>Video title</video:title>
   <video:description>Video desc</video:description>
   <video:content_loc>https://example.com/video.mp4</video:content_loc>
   <video:player_loc allow_embed=\"Yes\">https://example.com/player.swf</video:player_loc>
   <video:live>No</video:live>
   <video:duration>10</video:duration>
   <video:platform>allow</video:platform>
   <video:requires_subscription>No</video:requires_subscription>
   <video:price currency=\"PLN\" type=\"rent\" resolution=\"HD\">10.00</video:price>
   <video:gallery_loc>https://example.com/upload-gallery</video:gallery_loc>
   <video:tag>tag1</video:tag>
   <video:tag>tag2</video:tag>
   <video:category>Category</video:category>
   <video:family_friendly>Yes</video:family_friendly>
   <video:publication_date>2019-02-20</video:publication_date>
   <video:view_count>10</video:view_count>
   <video:uploader info=\"https://example.com/uploader-url\">Uploader</video:uploader>
   <video:rating>4.5</video:rating>
  </video:video>
 </url>
</urlset>", file_get_contents($dir . DIRECTORY_SEPARATOR . 'sitemap.xml'));
    }
}

<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;

/**
 * Class NewsTest
 *
 * @package Wszetko\Sitemap\Tests
 */
class NewsTest extends TestCase
{
    public function testConstructor()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-02-14'), 'Title');
        $this->assertInstanceOf(Items\News::class, $news);

        $news = new Items\News('News name', 'zh-cn', new DateTime('2014-02-14'), 'Title');
        $this->assertInstanceOf(Items\News::class, $news);

        $news = new Items\News('News name', 'zh-tw', new DateTime('2014-02-14'), 'Title');
        $this->assertInstanceOf(Items\News::class, $news);

        $news = new Items\News('News name', 'csb', new DateTime('2014-02-14'), 'Title');
        $this->assertInstanceOf(Items\News::class, $news);

        $news = new Items\News('News name', 'en', '2014-02-14', 'Title');
        $this->assertInstanceOf(Items\News::class, $news);
    }

    public function testConstructorExceptionName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid publication name parameter.');
        $news = new Items\News('', 'en', '2014-02-14', 'Title');
    }

    public function testConstructorExceptionLang()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid publication lang parameter.');
        new Items\News('News name', 'invalid', '2014-02-14', 'Title');
    }

    public function testConstructorExceptionInvalidDate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date parameter.');
        new Items\News('News name', 'en', '0000-00-00', 'Title');
    }

    public function testConstructorExceptionNoDate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date parameter.');
        new Items\News('News name', 'en', 'Thi is no date', 'Title');
    }

    public function testGetPublicationName()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $this->assertEquals('News name', $news->getPublicationName());
    }

    public function testGetPublicationLanguage()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $this->assertEquals('pl', $news->getPublicationLanguage());
    }

    public function testGetPublicationDate()
    {
        date_default_timezone_set('Europe/London');

        $news = new Items\News('News name', 'pl', new DateTime('2013-11-01'), 'Title');

        $this->assertEquals('2013-11-01', $news->getPublicationDate());

        $news = new Items\News('News name', 'pl', '2015-09-05', 'Title');

        $this->assertEquals('2015-09-05', $news->getPublicationDate());

        $news = new Items\News('News name', 'pl', new DateTime('2013-11-01 16:40:00'), 'Title');

        $this->assertEquals('2013-11-01T16:40:00+00:00', $news->getPublicationDate());
    }

    public function testGetTitle()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $this->assertEquals('Title', $news->getTitle());
    }

    public function testAccess()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $news->setAccess('Subscription');
        $this->assertEquals('Subscription', $news->getAccess());

        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $news->setAccess('Registration');
        $this->assertEquals('Registration', $news->getAccess());

        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $news->setAccess('Invalid');
        $this->assertNull($news->getAccess());
    }

    public function testGenres()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addGenres('Blog');
        $this->assertEquals('Blog', $news->getGenres());

        $news->addGenres('Invalid entry');
        $this->assertEquals('Blog', $news->getGenres());

        $news->addGenres('Invalid entries, more then one');
        $this->assertEquals('Blog', $news->getGenres());

        $news->addGenres('PressRelease');
        $this->assertEquals('Blog, PressRelease', $news->getGenres());

        $news->addGenres('Opinion, UserGenerated');
        $this->assertEquals('Blog, PressRelease, Opinion, UserGenerated', $news->getGenres());
    }

    public function testKeywords()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addKeywords('Test1');
        $this->assertEquals('Test1', $news->getKeywords());

        $news->addKeywords('Test2, Test3,Test4');
        $this->assertEquals('Test1, Test2, Test3, Test4', $news->getKeywords());
    }

    public function testStockTickers()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addStockTickers('NASDAQ:AMAT');
        $this->assertEquals('NASDAQ:AMAT', $news->getStockTickers());

        $news->addStockTickers('BOM:500325');
        $this->assertEquals('NASDAQ:AMAT, BOM:500325', $news->getStockTickers());

        $news->addStockTickers('NASDAQ:AMD, NASDAQ:GOOG,NASDAQ:MSFT, NASDAQ:AITX');
        $this->assertEquals('NASDAQ:AMAT, BOM:500325, NASDAQ:AMD, NASDAQ:GOOG, NASDAQ:MSFT', $news->getStockTickers());
    }

    public function testToArray()
    {
        date_default_timezone_set('Europe/London');

        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $news->setAccess('Subscription');
        $news->addGenres('Blog');
        $news->addKeywords('Test1');
        $news->addStockTickers('NASDAQ:AMAT');

        $expectedResult = [
            '_namespace' => $news::NAMESPACE_NAME,
            '_element' => 'news',
            'news' => [
                'publication' => [
                    'name' => 'News name',
                    'language' => 'pl'
                ],
                'publication_date' => '2014-08-01',
                'title' => 'Title',
                'access' => 'Subscription',
                'genres' => 'Blog',
                'keywords' => 'Test1',
                'stock_tickers' => 'NASDAQ:AMAT'
            ]
        ];

        $this->assertEquals($expectedResult, $news->toArray());
    }
}

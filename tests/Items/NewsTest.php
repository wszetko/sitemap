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

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items;

/**
 * Class NewsTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class NewsTest extends TestCase
{
    /**
     * @dataProvider constructorProvider
     *
     * @param mixed $name
     * @param mixed $lang
     * @param mixed $date
     * @param mixed $title
     *
     * @throws \ReflectionException
     */
    public function testConstructor($name, $lang, $date, $title)
    {
        $news = new Items\News($name, $lang, $date, $title);
        $this->assertInstanceOf(Items\News::class, $news);
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function constructorProvider()
    {
        return [
            ['News name', 'pl', new DateTime('2014-02-14'), 'Title'],
            ['News name', 'zh-cn', new DateTime('2014-02-14'), 'Title'],
            ['News name', 'zh-tw', new DateTime('2014-02-14'), 'Title'],
            ['News name', 'csb', new DateTime('2014-02-14'), 'Title'],
            ['News name', 'en', '2014-02-14', 'Title'],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorExceptionName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('publicationName need to be set.');
        $news = new Items\News('', 'en', '2014-02-14', 'Title');
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorExceptionLangCase1()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('publicationLanguage need to be set.');
        new Items\News('News name', 'invalid', '2014-02-14', 'Title');
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorExceptionLangCase2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('publicationLanguage need to be set.');
        new Items\News('News name', '', '2014-02-14', 'Title');
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorExceptionInvalidDate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date parameter.');
        new Items\News('News name', 'en', '0000-00-00', 'Title');
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorExceptionNoDate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date parameter.');
        new Items\News('News name', 'en', 'Thi is no date', 'Title');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetPublicationName()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $this->assertEquals('News name', $news->getPublicationName());
    }

    /**
     * @dataProvider getPublicationLanguageProvider
     *
     * @param mixed $lang
     * @param mixed $expected
     *
     * @throws \ReflectionException
     */
    public function testGetPublicationLanguage($lang, $expected)
    {
        $news = new Items\News('News name', $lang, new DateTime('2014-08-01'), 'Title');
        $this->assertEquals($expected, $news->getPublicationLanguage());
    }

    public function getPublicationLanguageProvider()
    {
        return [
            ['pl', 'pl'],
            ['PL', 'pl'],
        ];
    }

    /**
     * @dataProvider getPublicationDateProvider
     *
     * @param mixed  $date
     * @param string $expected
     *
     * @throws \ReflectionException
     */
    public function testGetPublicationDate($date, string $expected)
    {
        $news = new Items\News('News name', 'pl', $date, 'Title');
        $this->assertEquals($expected, $news->getPublicationDate());
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function getPublicationDateProvider()
    {
        date_default_timezone_set('Europe/London');

        return [
            [new DateTime('2013-11-01'), '2013-11-01'],
            ['2015-09-05', '2015-09-05'],
            [new DateTime('2013-11-01 16:40:00'), '2013-11-01T16:40:00+00:00'],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetTitle()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $this->assertEquals('Title', $news->getTitle());
    }

    /**
     * @throws \ReflectionException
     */
    public function testAccessSubscription()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $news->setAccess('Subscription');
        $this->assertEquals('Subscription', $news->getAccess());
    }

    /**
     * @throws \ReflectionException
     */
    public function testAccessRegistration()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $news->setAccess('Registration');
        $this->assertEquals('Registration', $news->getAccess());
    }

    /**
     * @throws \ReflectionException
     */
    public function testAccessInvalid()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');
        $news->setAccess('Invalid');
        $this->assertNull($news->getAccess());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenresCase1()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addGenres('Blog');
        $this->assertEquals(['Blog'], $news->getGenres());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenresCase2()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addGenres('Blog');
        $this->assertEquals(['Blog'], $news->getGenres());

        $news->addGenres('Invalid entry');
        $this->assertEquals(['Blog'], $news->getGenres());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenresCase3()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addGenres('Blog');
        $this->assertEquals(['Blog'], $news->getGenres());

        $news->addGenres('Invalid entry');
        $this->assertEquals(['Blog'], $news->getGenres());

        $news->addGenres('PressRelease');
        $this->assertEquals(['Blog', 'PressRelease'], $news->getGenres());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenresCase4()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addGenres('Blog');
        $this->assertEquals(['Blog'], $news->getGenres());

        $news->addGenres('Invalid entry');
        $this->assertEquals(['Blog'], $news->getGenres());

        $news->addGenres('PressRelease');
        $this->assertEquals(['Blog', 'PressRelease'], $news->getGenres());

        $news->addGenres(['Opinion', 'UserGenerated']);
        $this->assertEquals(['Blog', 'PressRelease', 'Opinion', 'UserGenerated'], $news->getGenres());
    }

    /**
     * @throws \ReflectionException
     */
    public function testKeywordsCase1()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addKeywords('Test1');
        $this->assertEquals(['Test1'], $news->getKeywords());
    }

    /**
     * @throws \ReflectionException
     */
    public function testKeywordsCase2()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addKeywords('Test1');
        $this->assertEquals(['Test1'], $news->getKeywords());

        $news->addKeywords(['Test2', 'Test3', ' Test4']);
        $this->assertEquals(['Test1', 'Test2', 'Test3', 'Test4'], $news->getKeywords());
    }

    /**
     * @throws \ReflectionException
     */
    public function testStockTickersCase1()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addStockTickers('NASDAQ:AMAT');
        $this->assertEquals(['NASDAQ:AMAT'], $news->getStockTickers());
    }

    /**
     * @throws \ReflectionException
     */
    public function testStockTickersCase2()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addStockTickers('NASDAQ:AMAT');
        $this->assertEquals(['NASDAQ:AMAT'], $news->getStockTickers());

        $news->addStockTickers('BOM:500325');
        $this->assertEquals(['NASDAQ:AMAT', 'BOM:500325'], $news->getStockTickers());
    }

    /**
     * @throws \ReflectionException
     */
    public function testStockTickersCase3()
    {
        $news = new Items\News('News name', 'pl', new DateTime('2014-08-01'), 'Title');

        $news->addStockTickers('NASDAQ:AMAT');
        $this->assertEquals(['NASDAQ:AMAT'], $news->getStockTickers());

        $news->addStockTickers('BOM:500325');
        $this->assertEquals(['NASDAQ:AMAT', 'BOM:500325'], $news->getStockTickers());

        $news->addStockTickers(['NASDAQ:AMD', 'NASDAQ:GOOG', 'NASDAQ:MSFT', 'NASDAQ:AITX']);
        $this->assertEquals(
            ['NASDAQ:AMAT', 'BOM:500325', 'NASDAQ:AMD', 'NASDAQ:GOOG', 'NASDAQ:MSFT'],
            $news->getStockTickers()
        );
    }

    /**
     * @throws \ReflectionException
     */
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
                    'language' => 'pl',
                ],
                'publication_date' => '2014-08-01',
                'title' => 'Title',
                'access' => 'Subscription',
                'genres' => 'Blog',
                'keywords' => 'Test1',
                'stock_tickers' => 'NASDAQ:AMAT',
            ],
        ];

        $this->assertEquals($expectedResult, $news->toArray());
    }
}

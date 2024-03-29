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

namespace Wszetko\Sitemap\Items;

use DateTimeInterface;

/**
 * Class News.
 *
 * @package Wszetko\Sitemap\Items
 *
 * @method setPublicationDate($publicationDate)
 * @method getPublicationDate()
 * @method setPublicationName($publicationName)
 * @method getPublicationName()
 * @method setPublicationLanguage($publicationLanguage)
 * @method getPublicationLanguage()
 * @method setAccess($access)
 * @method getAccess()
 * @method setTitle($title)
 * @method getTitle()
 * @method setGenres($genres)
 * @method addGenres($genres)
 * @method getGenres()
 * @method setKeywords($keywords)
 * @method addKeywords($keywords)
 * @method getKeywords()
 * @method setStockTickers($stockTickers)
 * @method addStockTickers($stockTickers)
 * @method getStockTickers()
 */
class News extends Extension
{
    /**
     * Name of Namescapce.
     */
    public const NAMESPACE_NAME = 'news';

    /**
     * Namespace URL.
     */
    public const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-news/0.9';

    /**
     * Element name.
     */
    public const ELEMENT_NAME = 'news';

    /**
     * Publication name.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $publicationName;

    /**
     * Publication language.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $publicationLanguage;

    /**
     * Access.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $access;

    /**
     * List of genres, comma-separated string values.
     *
     * @dataType \Wszetko\Sitemap\Items\DataTypes\StringType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\ArrayType
     */
    protected $genres;

    /**
     * Date of publication.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\DateTimeType
     */
    protected $publicationDate;

    /**
     * Title.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $title;

    /**
     * Key words, comma-separated string values.
     *
     * @dataType \Wszetko\Sitemap\Items\DataTypes\StringType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\ArrayType
     */
    protected $keywords;

    /**
     * Key words, comma-separated string values.
     *
     * @dataType \Wszetko\Sitemap\Items\DataTypes\StringType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\ArrayType
     */
    protected $stockTickers;

    /**
     * News constructor.
     *
     * @param string                   $publicationName
     * @param string                   $publicationLanguage
     * @param DateTimeInterface|string $publicationDate
     * @param string                   $title
     *
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     * @throws \Error
     */
    public function __construct(
        string $publicationName,
        string $publicationLanguage,
        $publicationDate,
        string $title
    ) {
        parent::__construct();

        $this->publicationName->setRequired(true);
        $this->setPublicationName($publicationName);
        $this->publicationLanguage
            ->setConversion('lower')
            ->setValueRegex("/^(zh-cn|zh-tw|([a-z]{2,3}))?$/")
            ->setRequired(true)
        ;
        $this->publicationDate->setRequired(true);
        $this->setPublicationLanguage($publicationLanguage);
        $this->setPublicationDate($publicationDate);
        $this->setTitle($title);
        $this->access->setAllowedValues('Subscription, Registration');
        /** @var \Wszetko\Sitemap\Items\DataTypes\StringType $generesValue */
        $generesValue = $this->genres->getBaseDataType();
        $generesValue->setAllowedValues('PressRelease, Satire, Blog, OpEd, Opinion, UserGenerated');
        $this->stockTickers->setMaxElements(5);
        /** @var \Wszetko\Sitemap\Items\DataTypes\StringType $stickTickersValue */
        $stickTickersValue = $this->stockTickers->getBaseDataType();
        $stickTickersValue->setValueRegex("/^(\\w+:\\w+)?$/");
    }

    /**
     * @return array
     *
     * @throws \Error
     */
    public function toArray(): array
    {
        $array = [
            '_namespace' => static::NAMESPACE_NAME,
            '_element' => 'news',
            'news' => [
                'publication' => [
                    'name' => $this->getPublicationName(),
                    'language' => $this->getPublicationLanguage(),
                ],
                'publication_date' => $this->getPublicationDate(),
                'title' => $this->getTitle(),
            ],
        ];

        if ($this->getAccess()) {
            $array['news']['access'] = $this->getAccess();
        }

        if ($this->getGenres()) {
            $array['news']['genres'] = implode(', ', $this->getGenres());
        }

        if ($this->getKeywords()) {
            $array['news']['keywords'] = implode(',', $this->getKeywords());
        }

        if ($this->getStockTickers()) {
            $array['news']['stock_tickers'] = implode(', ', $this->getStockTickers());
        }

        return $array;
    }
}

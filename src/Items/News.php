<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

use DateTimeInterface;
use InvalidArgumentException;
use Wszetko\Sitemap\Traits\DateTime;

/**
 * Class News
 *
 * @package Wszetko\Sitemap\Items
 */
class News extends Extension
{
    use DateTime;

    /**
     * Name of Namescapce
     */
    const NAMESPACE_NAME = 'news';

    /**
     * Namespace URL
     */
    const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-news/0.9';

    /**
     * Publication name.
     *
     * @var string
     */
    protected $publicationName;

    /**
     * Publication language.
     *
     * @var string
     */
    protected $publicationLanguage;

    /**
     * Access.
     *
     * @var string
     */
    protected $access;

    /**
     * List of genres, comma-separated string values.
     *
     * @var array
     */
    protected $genres = [];

    /**
     * Date of publication.
     *
     * @var string
     */
    protected $publicationDate;

    /**
     * Title.
     *
     * @var string
     */
    protected $title;

    /**
     * Key words, comma-separated string values.
     *
     * @var array
     */
    protected $keywords;

    /**
     * Key words, comma-separated string values.
     *
     * @var array
     */
    protected $stockTickers;


    /**
     * News constructor.
     *
     * @param string $publicationName
     * @param string $publicationLanguage
     * @param DateTimeInterface|string $publicationDate
     * @param string $title
     */
    public function __construct(
        string $publicationName,
        string $publicationLanguage,
        $publicationDate,
        string $title
    ) {
        if (!empty($publicationName)) {
            $this->publicationName = $publicationName;
        } else {
            throw new InvalidArgumentException('Invalid publication name parameter.');
        }

        preg_match_all("/^(?'lang'zh-cn|zh-tw|([a-z]{2,3}))?$/", $publicationLanguage, $matches);

        if (!empty($matches['lang'])) {
            $this->publicationLanguage = $publicationLanguage;
        } else {
            throw new InvalidArgumentException('Invalid publication lang parameter.');
        }

        $this->publicationDate = $this->processDateTime($publicationDate, true);

        $this->title = $title;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            '_namespace' => static::NAMESPACE_NAME,
            '_element' => 'news',
            'news' => [
                'publication' => [
                    'name' => $this->getPublicationName(),
                    'language' => $this->getPublicationLanguage()
                ],
                'publication_date' => $this->getPublicationDate(),
                'title' => $this->getTitle()
            ]
        ];

        if ($this->getAccess()) {
            $array['news']['access'] = $this->getAccess();
        }

        if ($this->getGenres()) {
            $array['news']['genres'] = $this->getGenres();
        }

        if ($this->getKeywords()) {
            $array['news']['keywords'] = $this->getKeywords();
        }

        if ($this->getStockTickers()) {
            $array['news']['stock_tickers'] = $this->getStockTickers();
        }

        return $array;
    }

    /**
     * Publication name.
     *
     * @return string
     */
    public function getPublicationName()
    {
        return $this->publicationName;
    }

    /**
     * Publication language.
     *
     * @return string
     */
    public function getPublicationLanguage()
    {
        return $this->publicationLanguage;
    }

    /**
     * Date of publication.
     *
     * @return string
     */
    public function getPublicationDate(): string
    {
        return $this->publicationDate;
    }

    /**
     * Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Access.
     *
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Set access.
     *
     * @param string $access
     *
     * @return $this
     */
    public function setAccess(string $access): self
    {
        if (in_array($access, ['Subscription', 'Registration'])) {
            $this->access = $access;
        } else {
            $this->access = null;
        }

        return $this;
    }

    /**
     * List of genres, comma-separated string values.
     *
     * @return string
     */
    public function getGenres()
    {
        return implode(', ', $this->genres);
    }

    /**
     * Key words, comma-separated string values.
     *
     * @return string
     */
    public function getKeywords()
    {
        return implode(', ', $this->keywords);
    }

    /**
     * @return string
     */
    public function getStockTickers(): string
    {
        return implode(', ', array_slice($this->stockTickers, 0, 5));
    }

    /**
     * Set list of genres, comma-separated string values.
     *
     * @param string $genres
     *
     * @return self
     */
    public function addGenres(string $genres): self
    {
        $genres = explode(',', $genres);

        foreach ($genres as $genre) {
            $genre = trim($genre);
            if (in_array($genre, ['PressRelease', 'Satire', 'Blog', 'OpEd', 'Opinion', 'UserGenerated'])) {
                $this->genres[] = trim($genre);
            }
        }

        return $this;
    }

    /**
     * Set key words, comma-separated string values.
     *
     * @param string $keywords
     *
     * @return self
     */
    public function addKeywords($keywords): self
    {
        $keywords = explode(',', $keywords);

        foreach ($keywords as $keyword) {
            $this->keywords[] = trim($keyword);
        }

        return $this;
    }

    /**
     * @param $stockTickers
     *
     * @return self
     */
    public function addStockTickers($stockTickers): self
    {
        preg_match_all("/^(?'stockTickers'\w+:\w+(, *\w+:\w+){0,4})?$/", $stockTickers, $matches);

        if (!empty($matches['stockTickers'])) {
            $matches = explode(',', $matches['stockTickers'][0]);
            foreach ($matches as $match) {
                $this->stockTickers[] = trim($match);
            }
        }

        return $this;
    }
}

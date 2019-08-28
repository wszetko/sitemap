<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

use DateTimeInterface;
use InvalidArgumentException;

class News extends Extension
{
    const NAMESPACE_NAME = 'news';

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
     * @var string
     */
    protected $genres;

    /**
     * Date of publication.
     *
     * @var \DateTimeInterface
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
     * @var string
     */
    protected $keywords;

    /**
     *
     */
    protected $stockTickers;


    public function __construct(
        string $publicationName,
        string $publicationLanguage,
        DateTimeInterface $publicationDate,
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

        $this->publicationDate = $publicationDate;
        $this->title = $title;
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
        return $this->genres;
    }

    /**
     * Set list of genres, comma-separated string values.
     *
     * @param string $genres
     *
     * @return self
     */
    public function setGenres(string $genres): self
    {
        preg_match_all("/^(?'genres'(PressRelease|Satire|Blog|OpEd|Opinion|UserGenerated)(, *(PressRelease|Satire|Blog|OpEd|Opinion|UserGenerated)))*$/",
            $genres, $matches);

        if (!empty($matches['genres'])) {
            $this->genres = $genres;
        }

        return $this;
    }

    /**
     * Date of publication.
     *
     * @return string|null
     */
    public function getPublicationDate(): ?string
    {
        if (!empty($this->publicationDate)) {
            if ($this->publicationDate->format('H') == 0 &&
                $this->publicationDate->format('i') == 0 &&
                $this->publicationDate->format('s') == 0) {
                return $this->publicationDate->format("Y-m-d");
            } else {
                return $this->publicationDate->format(DateTimeInterface::W3C);
            }
        } else {
            return null;
        }
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
     * Key words, comma-separated string values.
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set key words, comma-separated string values.
     *
     * @param string $keywords
     *
     * @return self
     */
    public function setKeywords($keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStockTickers()
    {
        return $this->stockTickers;
    }


    /**
     * @param $stockTickers
     *
     * @return self
     */
    public function setStockTickers($stockTickers): self
    {
        preg_match_all("/^(?'stockTickers'\w+:\w+(, *\w+:\w+){0,4})?$/", $stockTickers, $matches);

        if (!empty($matches['stockTickers'])) {
            $this->stockTickers = $stockTickers;
        }

        return $this;
    }

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
}

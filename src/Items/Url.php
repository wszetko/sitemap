<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

use DateTimeInterface;
use InvalidArgumentException;
use Wszetko\Sitemap\Interfaces\Item;
use Wszetko\Sitemap\Sitemap;
use Wszetko\Sitemap\Traits\DateTime;

/**
 * Class Url
 *
 * @package Wszetko\Sitemap\Items
 */
class Url implements Item
{
    use DateTime;

    /**
     * Domain
     *
     * @var string
     */
    private $domain = '';

    /**
     * Location (URL)
     *
     * @var string
     */
    private $loc;

    /**
     * Last modified time
     *
     * @var string
     */
    private $lastMod;

    /**
     * Change frequency of the location
     *
     * @var string|null
     */
    private $changeFreq = null;

    /**
     * Priority of page importance
     *
     * @var float|null
     */
    private $priority = null;

    /**
     * Array of used extensions
     *
     * @var Extension[]
     */
    private $extensions = [];

    /**
     * Url constructor.
     *
     * @param string $loc
     */
    public function __construct(string $loc)
    {
        $this->loc = '/' . ltrim($loc, '/');
    }

    public function toArray(): array
    {
        $array = [
            'loc' => $this->getLoc()
        ];

        if ($this->getLastMod()) {
            $array['lastmod'] = $this->getLastMod();
        }

        if ($this->getChangeFreq()) {
            $array['changefreq'] = $this->getChangeFreq();
        }

        if ($this->getPriority()) {
            $array['priority'] = $this->getPriority();
        }

        foreach ($this->getExtensions() as $extension => $data) {
            $data->setDomain($this->getDomain());
            $array[$extension] = $data->toArray();
        }

        return $array;
    }

    /**
     * @return string
     */
    public function getLoc(): string
    {
        return $this->getDomain() . $this->loc;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return self
     */
    public function setDomain(string $domain): self
    {
        if ($domain = \Wszetko\Sitemap\Helpers\Url::normalizeUrl($domain)) {
            $this->domain = rtrim($domain, '/');
        } else {
            throw new InvalidArgumentException('Parameter $domain need to be valid domain name.');
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastMod(): ?string
    {
        return $this->lastMod;
    }

    /**
     * @param DateTimeInterface|string $lastMod
     *
     * @return self
     */
    public function setLastMod($lastMod): self
    {
        $this->lastMod = $this->processDateTime($lastMod);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getChangeFreq(): ?string
    {
        return $this->changeFreq;
    }

    /**
     * @param string|null $changeFreq
     *
     * @return self
     */
    public function setChangeFreq(?string $changeFreq): self
    {
        if (in_array($changeFreq, Sitemap::CHANGEFREQ)) {
            $this->changeFreq = $changeFreq;
        } else {
            $this->changeFreq = null;
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->priority !== null ? number_format($this->priority, 1) : null;
    }

    /**
     * @param int|float|null $priority
     *
     * @return self
     */
    public function setPriority($priority): self
    {
        if ($priority >= 0 && $priority <= 1) {
            $this->priority = $priority;
        } else {
            $this->priority = null;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param Extension $extension
     *
     * @return self
     */
    public function addExtension(Extension $extension): self
    {
        $this->extensions[$extension::NAMESPACE_NAME] = $extension;

        return $this;
    }
}

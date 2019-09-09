<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

use Wszetko\Sitemap\Sitemap;
use Wszetko\Sitemap\Traits\Domain;

/**
 * Class Url
 *
 * @package Wszetko\Sitemap\Items
 *
 * @method setLoc($loc): self
 * @method getLoc()
 * @method setPriority($priority): self
 * @method getPriority()
 * @method setChangefreq($changeFreq): self
 * @method getChangefreq()
 * @method setLastmod($lastMod): self
 * @method getLastmod()
 */
class Url extends AbstractItem
{
    use Domain;

    /**
     * Element name
     */
    const ELEMENT_NAME = 'url';

    /**
     * Location (URL)
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\URLType
     */
    protected $loc;

    /**
     * Last modified time
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\DateTimeType
     */
    protected $lastmod;

    /**
     * Change frequency of the location
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $changefreq;

    /**
     * Priority of page importance
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\FloatType
     */
    protected $priority;

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
     *
     * @throws \ReflectionException
     */
    public function __construct(string $loc)
    {
        parent::__construct();

        $this->loc->setRequired(true);
        $this->setLoc($loc);
        $this->changefreq->setAllowedValues(Sitemap::CHANGEFREQ);
        $this->priority
            ->setMinValue(0)
            ->setMaxValue(1)
            ->setPrecision(1);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        foreach ($this->getExtensions() as $extension => $data) {
            foreach ($data as $ext) {
                $ext->setDomain($this->getDomain());
                $array['url'][$extension][] = $ext->toArray();
            }
        }

        return $array;
    }

    /**
     * @return Extension[]
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
        if (!isset($this->extensions[$extension::NAMESPACE_NAME])) {
            $this->extensions[$extension::NAMESPACE_NAME] = [];
        }

        $this->extensions[$extension::NAMESPACE_NAME][] = $extension;

        return $this;
    }
}

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

use Wszetko\Sitemap\Sitemap;

/**
 * Class Url.
 *
 * @package Wszetko\Sitemap\Items
 *
 * @method setLoc($loc): static
 * @method getLoc()
 * @method setPriority($priority): static
 * @method getPriority()
 * @method setChangefreq($changeFreq): static
 * @method getChangefreq()
 * @method setLastmod($lastMod): static
 * @method getLastmod()
 */
class Url extends AbstractItem
{
    /**
     * Element name.
     */
    public const ELEMENT_NAME = 'url';

    /**
     * Location (URL).
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\URLType
     */
    protected $loc;

    /**
     * Last modified time.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\DateTimeType
     */
    protected $lastmod;

    /**
     * Change frequency of the location.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $changefreq;

    /**
     * Priority of page importance.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\FloatType
     */
    protected $priority;

    /**
     * Array of used extensions.
     *
     * @var array
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
            ->setPrecision(1)
        ;
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
        if (!isset($this->extensions[$extension::NAMESPACE_NAME])) {
            $this->extensions[$extension::NAMESPACE_NAME] = [];
        }

        $this->extensions[$extension::NAMESPACE_NAME][] = $extension;

        return $this;
    }
}

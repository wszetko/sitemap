<?php

namespace Wszetko\Sitemap\Items;

use Wszetko\Sitemap\Interfaces\Item;

/**
 * Class Extension
 *
 * @package Wszetko\Sitemap\Items
 */
abstract class Extension implements Item
{
    const NAMESPACE_NAME = null;

    const NAMESPACE_URL = null;

    /**
     * Domain
     *
     * @var string
     */
    private $domain = '';

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }
}

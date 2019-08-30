<?php

namespace Wszetko\Sitemap\Items;

use Wszetko\Sitemap\Interfaces\Item;
use Wszetko\Sitemap\Traits\Domain;

/**
 * Class Extension
 *
 * @package Wszetko\Sitemap\Items
 */
abstract class Extension implements Item
{
    use Domain;

    /**
     * Name of Namescapce
     */
    const NAMESPACE_NAME = null;

    /**
     * Namescapce URL
     */
    const NAMESPACE_URL = null;
}

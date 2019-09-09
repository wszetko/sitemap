<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Interfaces;

/**
 * Interface XML
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface Item
{
    /**
     * Name of Namescapce
     */
    const NAMESPACE_NAME = null;

    /**
     * Namescapce URL
     */
    const NAMESPACE_URL = null;

    /**
     * Element name
     */
    const ELEMENT_NAME = null;

    /**
     * @return array
     */
    public function toArray(): array;
}

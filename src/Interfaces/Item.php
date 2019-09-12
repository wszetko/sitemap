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

namespace Wszetko\Sitemap\Interfaces;

/**
 * Interface XML.
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface Item
{
    /**
     * Name of Namescapce.
     */
    public const NAMESPACE_NAME = null;

    /**
     * Namescapce URL.
     */
    public const NAMESPACE_URL = null;

    /**
     * Element name.
     */
    public const ELEMENT_NAME = null;

    /**
     * @return array
     */
    public function toArray(): array;
}

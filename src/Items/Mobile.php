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

/**
 * Class Mobile.
 *
 * @package Wszetko\Sitemap\Items
 */
class Mobile extends Extension
{
    /**
     * Name of Namescapce.
     */
    public const NAMESPACE_NAME = 'mobile';

    /**
     * Namespace URL.
     */
    public const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-mobile/1.0';

    /**
     * Element name.
     */
    public const ELEMENT_NAME = 'mobile';
}

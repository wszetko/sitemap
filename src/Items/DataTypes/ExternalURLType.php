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

namespace Wszetko\Sitemap\Items\DataTypes;

/**
 * Class ExternalURLType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class ExternalURLType extends URLType
{
    /**
     * Determine if URL CAN be external.
     *
     * @var bool
     */
    protected $external = true;
}

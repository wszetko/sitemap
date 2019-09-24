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

namespace Wszetko\Sitemap\Traits;

use InvalidArgumentException;
use Wszetko\Sitemap\Helpers\Url;

/**
 * Trait Domain.
 *
 * @package Wszetko\Sitemap\Traits
 */
trait Domain
{
    /**
     * Domain.
     *
     * @var null|string
     */
    private $domain;

    /**
     * @return null|string
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param null|string $domain
     *
     * @return static
     */
    public function setDomain(?string $domain)
    {
        $domain = Url::normalizeUrl((string) $domain);

        if (is_string($domain)) {
            $this->domain = rtrim($domain, '/');
        } else {
            throw new InvalidArgumentException('Domain name is not valid.');
        }

        return $this;
    }
}

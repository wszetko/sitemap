<?php

namespace Wszetko\Sitemap\Traits;

use InvalidArgumentException;
use Wszetko\Sitemap\Helpers\Url;

/**
 * Trait Domain
 *
 * @package Wszetko\Sitemap\Traits
 */
trait Domain
{
    /**
     * Domain
     *
     * @var string
     */
    private $domain;

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param string|null $domain
     *
     * @return self
     */
    public function setDomain(?string $domain): self
    {
        if ($domain = Url::normalizeUrl((string) $domain)) {
            $this->domain = rtrim($domain, '/');
        } else {
            throw new InvalidArgumentException('Domain name is not valid.');
        }

        return $this;
    }
}

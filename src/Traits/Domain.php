<?php


namespace Wszetko\Sitemap\Traits;

use InvalidArgumentException;

trait Domain
{
    /**
     * Domain
     *
     * @var string
     */
    private $domain;

    private $domainIsRequired = false;

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return self
     */
    public function setDomain(string $domain): self
    {
        if ($domain = \Wszetko\Sitemap\Helpers\Url::normalizeUrl($domain)) {
            $this->domain = rtrim($domain, '/');
        } elseif ($this->domainIsRequired) {
            throw new InvalidArgumentException('Domain name is not valid.');
        }

        return $this;
    }
}
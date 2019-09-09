<?php

namespace Wszetko\Sitemap\Traits;

/**
 * Trait Required
 *
 * @package Wszetko\Sitemap\Traits
 */
trait Required
{
    /**
     * @var bool
     */
    private $required = false;

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return self
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }
}

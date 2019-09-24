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

/**
 * Trait Required.
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
     * @return static
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }
}

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

use Wszetko\Sitemap\Helpers\Url;

/**
 * Class URLType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class URLType extends StringType
{
    /**
     * Determine if URL CAN be external.
     *
     * @var bool
     */
    protected $external = false;

    /**
     * @return null|mixed|string
     */
    public function getValue()
    {
        if (null === $this->value || !is_string($this->value)) {
            return null;
        }

        if ($this->isExternal() && false !== Url::normalizeUrl($this->value)) {
            $value = $this->value;
        } else {
            $value = str_replace($this->getDomain(), '', $this->value);

            if (!empty($value)) {
                $value = $this->getDomain() . '/' . ltrim($value, '/');
            }
        }

        $attributes = $this->getAttributes();

        if (!empty($value) && !empty($attributes)) {
            return [$value => $attributes];
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function isExternal(): bool
    {
        return $this->external;
    }
}

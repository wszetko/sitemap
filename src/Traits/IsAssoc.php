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
 * Trait IsAssoc
 *
 * @package Wszetko\Sitemap\Traits
 */
trait IsAssoc
{
    /**
     * Check if array is associative.
     *
     * @param array $array
     *
     * @return bool
     */
    protected function isAssoc(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (!is_integer($key)) {
                return true;
            }
        }

        return false;
    }
}

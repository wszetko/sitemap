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

// For compatibility with PHP < 7.3
if (!function_exists('array_key_first')) {
    /**
     * Gets the first key of an array.
     *
     * @param array $arr
     *
     * @return null|string
     */
    function array_key_first(array $arr): ?string
    {
        foreach ($arr as $key => $unused) {
            return $key;
        }

        return null;
    }
}

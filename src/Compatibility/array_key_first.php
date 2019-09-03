<?php
declare(strict_types=1);

// For compatibility with PHP < 7.3
if (!function_exists('array_key_first')) {
    /**
     * Gets the first key of an array
     *
     * @param array $arr
     *
     * @return string|null
     */
    function array_key_first(array $arr): ?string
    {
        foreach ($arr as $key => $unused) {
            return $key;
        }

        return null;
    }
}

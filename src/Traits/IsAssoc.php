<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Traits;

trait IsAssoc
{
    /**
     * @param array $array
     *
     * @return bool
     */
    private function isAssoc(array $array): bool
    {
        foreach ($array as $key => $val) {
            if (!is_integer($key)) {
                return true;
            }
        }

        return false;
    }
}
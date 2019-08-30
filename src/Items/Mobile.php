<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

/**
 * Class Mobile
 *
 * @package Wszetko\Sitemap\Items
 */
class Mobile extends Extension
{
    /**
     * Name of Namescapce
     */
    const NAMESPACE_NAME = 'mobile';

    /**
     * Namespace URL
     */
    const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-mobile/1.0';

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            '_namespace' => static::NAMESPACE_NAME,
            '_element' => 'mobile',
            'mobile' => []
        ];

        return $array;
    }
}

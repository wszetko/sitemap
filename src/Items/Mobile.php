<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

class Mobile extends Extension
{
    const NAMESPACE_NAME = 'mobile';

    const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-mobile/1.0';

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
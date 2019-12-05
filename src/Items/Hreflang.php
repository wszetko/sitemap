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

namespace Wszetko\Sitemap\Items;

use InvalidArgumentException;
use Wszetko\Sitemap\Items\DataTypes\StringType;

/**
 * Class Hreflang.
 *
 * @package Wszetko\Sitemap\Items
 *
 * @method addHreflang($hreflang, $href)
 * @method setHreflang($hreflang, $href)
 * @method getHreflang()
 */
class Hreflang extends Extension
{
    /**
     * Name of Namescapce.
     */
    public const NAMESPACE_NAME = 'xhtml';

    /**
     * Namespace URL.
     */
    public const NAMESPACE_URL = 'http://www.w3.org/1999/xhtml';

    /**
     * Element name.
     */
    public const ELEMENT_NAME = 'link';

    /**
     * @dataType \Wszetko\Sitemap\Items\DataTypes\StringType
     * @attribute href
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\URLType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\ArrayType
     */
    protected $hreflang;

    /**
     * @param string $hreflang
     * @param string $href
     *
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     * @throws \Error
     */
    public function __construct(string $hreflang, string $href)
    {
        parent::__construct();

        /** @var \Wszetko\Sitemap\Items\DataTypes\StringType $baseType */
        $baseType = $this->hreflang->getBaseDataType();
        $baseType
            ->setValueRegex("/^(([a-z]{2}|(x))((-)([A-Za-z]{2}|[A-Z]([a-z]|[a-z]{3})|(default)))?)$/")
            ->setRequired(true)
        ;
        /** @var \Wszetko\Sitemap\Items\DataTypes\URLType $hrefAttribute */
        $hrefAttribute = $baseType->getAttribute('href');
        $hrefAttribute->setRequired(true);
        $this->addHreflang($hreflang, $href);
    }

    /**
     * @return array
     *
     * @throws \Error
     */
    public function toArray(): array
    {
        $array = [
            '_namespace' => static::NAMESPACE_NAME,
            '_element' => 'link',
            'link' => [],
        ];

        foreach ($this->getHreflang() as $links) {
            foreach ($links as $hreflang => $lang) {
                $array['link'][] = [
                    '_attributes' => [
                        'rel' => 'alternate',
                        'hreflang' => $hreflang,
                        'href' => $lang['href'],
                    ],
                ];
            }
        }

        return $array;
    }
}

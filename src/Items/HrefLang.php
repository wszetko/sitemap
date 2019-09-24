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

use Wszetko\Sitemap\Items\DataTypes\StringType;

/**
 * Class HrefLang.
 *
 * @package Wszetko\Sitemap\Items
 *
 * @method addHrefLang($hrefLang, $href)
 * @method setHrefLang($hrefLang, $href)
 * @method getHrefLang()
 */
class HrefLang extends Extension
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
    protected $hrefLang;

    /**
     * @param string $hrefLang
     * @param string $href
     *
     * @throws \ReflectionException
     */
    public function __construct(string $hrefLang, string $href)
    {
        parent::__construct();

        if ($this->hrefLang->getBaseDataType() instanceof StringType) {
            $this->hrefLang
                ->getBaseDataType()
                ->setValueRegex(
                    "/^(?'hreflang'([a-z]{2}|(x))((-)([A-Za-z]{2}|[A-Z]([a-z]|[a-z]{3})|(default)))?)$/",
                    'hreflang'
                )
                ->setRequired(true)
                ->getAttribute('href')
                ->setRequired(true)
            ;
        } else {
            // @codeCoverageIgnoreStart
            throw new \InvalidArgumentException('Class is missconfigured.');
            // @codeCoverageIgnoreEnd
        }

        $this->addHrefLang($hrefLang, $href);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            '_namespace' => static::NAMESPACE_NAME,
            '_element' => 'link',
            'link' => [],
        ];

        foreach ($this->getHrefLang() as $hreflang => $lang) {
            $array['link'][] = [
                '_attributes' => [
                    'rel' => 'alternate',
                    'hreflang' => $hreflang,
                    'href' => $lang['href'],
                ],
            ];
        }

        return $array;
    }
}

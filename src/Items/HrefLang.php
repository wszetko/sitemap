<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

/**
 * Class HrefLang
 *
 * @package Wszetko\Sitemap\Items
 * @method addHrefLang($hrefLang, $href)
 * @method setHrefLang($hrefLang, $href)
 * @method getHrefLang()
 */
class HrefLang extends Extension
{
    /**
     * Name of Namescapce
     */
    const NAMESPACE_NAME = 'xhtml';

    /**
     * Namespace URL
     */
    const NAMESPACE_URL = 'http://www.w3.org/1999/xhtml';

    /**
     * Element name
     */
    const ELEMENT_NAME = 'link';

    /**
     * @dataType \Wszetko\Sitemap\Items\DataTypes\StringType
     * @attribute href
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\URLType
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

        $hrefLangValue = $this->hrefLang
            ->getBaseDataType();
        /** @var $hrefLang \Wszetko\Sitemap\Items\DataTypes\StringType */
        $hrefLangValue
            ->setRequired(true)
            ->setValueRegex("/^(?'hreflang'([a-z]{2}|(x))((-)([A-Za-z]{2}|[A-Z]([a-z]|[a-z]{3})|(default)))?)$/", 'hreflang')
            ->getAttribute('href')
            ->setRequired(true);

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
            'link' => []
        ];

        foreach ($this->getHrefLang() as $hreflang => $lang) {
            $array['link'][] = [
                '_attributes' => [
                    'rel' => 'alternate',
                    'hreflang' => $hreflang,
                    'href' => $lang['href']
                ]
            ];
        }

        return $array;
    }
}

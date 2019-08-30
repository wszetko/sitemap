<?php


namespace Wszetko\Sitemap\Items;

use InvalidArgumentException;

/**
 * Class HrefLang
 *
 * @package Wszetko\Sitemap\Items
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
     * @var array
     */
    private $hrefLang = [];

    /**
     * @param string $hrefLang
     * @param string $href
     *
     * @return self
     */
    public function __construct(string $hrefLang, string $href)
    {
        return $this->addHrefLang($hrefLang, $href);
    }

    /**
     * @param string $hrefLang
     * @param string $href
     *
     * @return self
     */
    public function addHrefLang(string $hrefLang, string $href): self
    {
        preg_match_all("/^(?'hreflang'([a-z]{2}|(x)){1}((-){1}([A-Za-z]{2}|[A-Z]{1}([a-z]{1}|[a-z]{3})|(default)))?)$/",
            $hrefLang, $matches);

        if (!empty($matches['hreflang'])) {
            $this->hrefLang[$hrefLang] = $href;
        } else {
            throw new InvalidArgumentException('Invalid hreflang parameter.');
        }

        return $this;
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

        foreach ($this->getHrefLangs() as $hreflang => $href) {
            $array['link'][] = [
                '_attributes' => [
                    'rel' => 'alternate',
                    'hreflang' => $hreflang,
                    'href' => $href
                ]
            ];
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getHrefLangs(): array
    {
        foreach ($this->hrefLang as &$href) {
            $href = $this->getDomain() . $href;
        }

        return $this->hrefLang;
    }
}

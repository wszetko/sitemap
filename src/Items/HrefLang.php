<?php


namespace Wszetko\Sitemap\Items;

use InvalidArgumentException;

class HrefLang extends Extension
{
    const NAMESPACE_NAME = 'xhtml';

    const NAMESPACE_URL = 'http://www.w3.org/1999/xhtml';

    /**
     * @var array
     */
    private $hrefLang = [];

    /**
     * @return array
     */
    public function get(): array
    {
        $result = [];

        foreach ($this->hrefLang as $hrefLang => $href) {
            $result[$hrefLang] = $this->getDomain() . $href;
        }
        return $result;
    }

    /**
     * @param string $hrefLang
     * @param string $href
     *
     * @return self
     */
    public function __construct(string $hrefLang, string $href)
    {
        preg_match_all("/^(?'hreflang'([a-z]{2}|(x)){1}((-){1}([A-Za-z]{2}|[A-Z]{1}([a-z]{1}|[a-z]{3})|(default)))?)$/", $hrefLang, $matches);

        if (!empty($matches['hreflang'])) {
            $this->hrefLang[$hrefLang] = $href;
        } else {
            throw new InvalidArgumentException('Invalid hreflang parameter.');
        }

        return $this;
    }

    public function toArray(): array
    {
        $array = [
            '_namespace' => static::NAMESPACE_NAME,
            '_element' => 'link',
            'link' => [
                '_attributes' => []
            ]
        ];

        foreach ($this->get() as $hreflang => $href) {
            $array['link']['_attributes'] = [
                'rel' => 'alternate',
                'hreflang' => $hreflang,
                'href' => $href
            ];
        }

        return $array;
    }
}

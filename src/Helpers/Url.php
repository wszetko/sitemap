<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Helpers;

/**
 * Class Url
 *
 * @package Wszetko\Sitemap\Helpers
 */
class Url
{
    /**
     * @param string $url
     *
     * @return bool|string
     * @see https://bugs.php.net/bug.php?id=52923
     * @see https://www.php.net/manual/en/function.parse-url.php#114817
     */
    public static function normalizeUrl(string $url)
    {
        $encodedUrl = preg_replace_callback(
            '%[^:/@?&=#]+%usD',
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $url
        );

        $url = parse_url($encodedUrl);

        if (empty($url) || !isset($url['host'])) {
            return false;
        }

        $url = array_map('urldecode', $url);
        $url['host'] = idn_to_ascii($url['host'], IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

        if (empty($url['scheme']) || !is_string($url['host']) || !self::checkDomain($url['host'])) {
            return false;
        }

        if (!empty($url['path'])) {
            $url['path'] = explode('/', $url['path']);
            $parts = [];

            foreach ($url['path'] as $element) {
                switch ($element) {
                    case '.':
                        break;
                    case '..':
                        array_pop($parts);
                        break;
                    default:
                        $parts[] = rawurlencode($element);
                        break;
                }
            }

            $url['path'] = implode('/', $parts);
        }

        if (!empty($url['query'])) {
            parse_str($url['query'], $query);
            array_walk($query, function (&$val, &$var) {
                $var = rawurlencode($var);
                $val = rawurlencode($val);
            });
            $url['query'] = http_build_query($query);
        }

        return
            $url['scheme'] . '://'
            . (isset($url['user']) ? $url['user'] . ((isset($url['pass'])) ? ':' . $url['pass'] : '') . '@' : '')
            . strtolower($url['host'])
            . ((isset($url['port'])) ? ':' . $url['port'] : '')
            . ((isset($url['path'])) ? $url['path'] : '')
            . ((isset($url['query'])) ? '?' . $url['query'] : '')
            . ((isset($url['fragment'])) ? '#' . $url['fragment'] : '');
    }

    /**
     * @param string $domain
     *
     * @return bool
     */
    public static function checkDomain(string $domain): bool
    {
        if (substr($domain, -1) == '.' || substr($domain, 0, 1) == '.') {
            return false;
        }

        $domain = idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

        return (bool) filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
    }
}

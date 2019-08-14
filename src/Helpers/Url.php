<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Helpers;

class Url
{
    static function check(string $url): bool {
        return (bool) filter_var(self::normalizeUrl($url), FILTER_VALIDATE_URL);
    }

    static function checkDomain(string $domain): bool {
        return (bool) filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
    }

    static function normalizeUrl(string $url): string
    {
        $url = parse_url($url);

        if (!$url || !isset($url['host'])) {
            return '';
        }

        $url['host'] = idn_to_ascii($url['host'], IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

        if (!self::checkDomain($url['host'])) {
            return '';
        }

        return
            $url['scheme'] . '://'
            .((isset($url['user'])) ? $url['user'] . ((isset($url['pass'])) ? ':' . $url['pass'] : '') .'@' : '')
            .$url['host']
            .((isset($url['port'])) ? ':' . $url['port'] : '')
            .((isset($url['path'])) ? $url['path'] : '')
            .((isset($url['query'])) ? '?' . $url['query'] : '')
            .((isset($url['fragment'])) ? '#' . $url['fragment'] : '');
    }
}
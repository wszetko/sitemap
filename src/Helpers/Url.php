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

namespace Wszetko\Sitemap\Helpers;

use League\Uri\Components\Domain;
use League\Uri\Components\Fragment;
use League\Uri\Components\Host;
use League\Uri\Components\Path;
use League\Uri\Components\Port;
use League\Uri\Components\Query;
use League\Uri\Components\Scheme;
use League\Uri\Components\UserInfo;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\Uri;
use League\Uri\UriModifier;
use League\Uri\UriString;

/**
 * Class Url.
 *
 * @package Wszetko\Sitemap\Helpers
 */
class Url
{
    /**
     * @param string $url
     *
     * @return bool|string
     *
     * @throws \League\Uri\Exceptions\SyntaxError
     */
    public static function normalizeUrl(string $url)
    {
        try {
            $uri = UriModifier::removeDotSegments(Uri::createFromString($url));
            $parts = [];
            $parts['scheme'] = Scheme::createFromUri($uri)->getContent();
            $parts['user'] = UserInfo::createFromUri($uri)->getUser();
            $parts['pass'] = UserInfo::createFromUri($uri)->getPass();
            $parts['host'] = Host::createFromUri($uri);
            $parts['port'] = Port::createFromUri($uri)->getContent();
            $parts['path'] = Path::createFromUri($uri)->getContent();
            $parts['query'] = Query::createFromUri($uri)->getContent();
            $parts['fragment'] = Fragment::createFromUri($uri)->getContent();

            if (
                '' === $parts['scheme'] ||
                null === $parts['scheme'] ||
                '' === $parts['host']->getContent() ||
                null === $parts['host']->getContent() ||
                (false === $parts['host']->isDomain() && false === $parts['host']->isIp())
            ) {
                return false;
            }

            if (true === $parts['host']->isDomain()) {
                $domain = new Domain($parts['host']);

                if (true === $domain->isAbsolute()) {
                    return false;
                }
            }

            $parts['host'] = (string) $parts['host'];

            return UriString::build($parts);
        } catch (\TypeError | SyntaxError $e) {
            return false;
        }
    }
}

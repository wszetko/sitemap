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

namespace Wszetko\Sitemap\Interfaces;

/**
 * Interface XML.
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface XML
{
    /**
     * XML constructor.
     *
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * @param string $dir
     */
    public function setWorkDir(string $dir): void;

    /**
     * @return null|string
     */
    public function getWorkDir(): ?string;

    /**
     * @return int
     */
    public function getSitemapSize(): int;

    /**
     * @param string $sitemap
     * @param array  $extensions
     */
    public function openSitemap(string $sitemap, array $extensions): void;

    public function closeSitemap(): void;

    /**
     * @param array $element
     *
     * @return mixed
     */
    public function addUrl(array $element);

    /**
     * @param string $sitemap
     */
    public function openSitemapIndex(string $sitemap): void;

    public function closeSitemapIndex(): void;

    /**
     * @param string      $sitemap
     * @param null|string $lastmod
     *
     * @return mixed
     */
    public function addSitemap(string $sitemap, ?string $lastmod = null);
}

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
     * Set path to directory on which class is working on.
     *
     * @param string $dir
     */
    public function setWorkDir(string $dir): void;

    /**
     * Get path to directory on which class is working on.
     *
     * @return null|string
     */
    public function getWorkDir(): ?string;

    /**
     * Return current sitemap file size.
     *
     * @return int
     */
    public function getSitemapSize(): int;

    /**
     * Make sitemap file and put proper tags on it.
     *
     * @param string $sitemap
     * @param array  $extensions
     */
    public function openSitemap(string $sitemap, array $extensions): void;

    /**
     * Close sitemap file and put proper tags on it.
     */
    public function closeSitemap(): void;

    /**
     * Add url to current sitemap.
     *
     * @param array $element
     *
     * @return mixed
     */
    public function addUrl(array $element);

    /**
     * Make sitemap index file and put proper tags on it.
     *
     * @param string $sitemap
     */
    public function openSitemapIndex(string $sitemap): void;

    /**
     * Close sitemap index file and put proper tags on it.
     */
    public function closeSitemapIndex(): void;

    /**
     * Add sitemap to sitemap index.
     *
     * @param string      $sitemap
     * @param null|string $lastmod
     *
     * @return mixed
     */
    public function addSitemap(string $sitemap, ?string $lastmod = null);
}

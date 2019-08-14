<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Interfaces;

/**
 * Interface XML
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface XML
{
    public function __construct(array $config);

    public function setWorkDir(string $dir): void;

    public function getWorkDir(): string;

    public function getSitemapSize(): int;

    public function openSitemap(string $sitemap, array $extensions): void;

    public function closeSitemap(): void;

    public function addUrl(array $element);

    public function openSitemapIndex(string $sitemap): void;

    public function closeSitemapIndex(): void;

    public function addSitemap(string $sitemap, ?string $lastmod = null);
}
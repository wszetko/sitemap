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

use Wszetko\Sitemap\Items\Url;

/**
 * Interface DataCollector.
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface DataCollector
{
    /**
     * @param Url    $item
     * @param string $group
     */
    public function add(Url $item, string $group): void;

    /**
     * @return array
     */
    public function getGroups(): array;

    /**
     * @param string $group
     *
     * @return array
     */
    public function fetch(string $group): ?array;

    /**
     * @param string $group
     *
     * @return array
     */
    public function fetchGroup(string $group): array;

    /**
     * @param null|string $group
     *
     * @return null|array
     */
    public function fetchAll(?string $group): ?array;

    /**
     * @param string $group
     *
     * @return bool
     */
    public function isLast(string $group): bool;

    /**
     * @return int
     */
    public function getGroupsCount(): int;

    /**
     * @param string $group
     *
     * @return int
     */
    public function getGroupCount(string $group): int;

    /**
     * @return int
     */
    public function getCount(): int;

    /**
     * @param array $extensions
     */
    public function addExtensions(array $extensions): void;

    /**
     * @return array
     */
    public function getExtensions(): array;
}

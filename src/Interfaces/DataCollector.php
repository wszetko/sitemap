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
     * Add URL to proper group.
     *
     * @param Url    $item
     * @param string $group
     */
    public function add(Url $item, string $group): void;

    /**
     * Return all groups names.
     *
     * @return array
     */
    public function getGroups(): array;

    /**
     * Fetch one element from group.
     *
     * @param string $group
     *
     * @return array
     */
    public function fetch(string $group): ?array;

    /**
     * Get all elements form group.
     *
     * @param string $group
     *
     * @return array
     */
    public function fetchGroup(string $group): array;

    /**
     * Get all elements by groups.
     *
     * @param null|string $group
     *
     * @return null|array
     */
    public function fetchAll(?string $group): ?array;

    /**
     * Check if current element is last one.
     *
     * @param string $group
     *
     * @return bool
     */
    public function isLast(string $group): bool;

    /**
     * Return number of groups.
     *
     * @return int
     */
    public function getGroupsCount(): int;

    /**
     * Return number of elements in group.
     *
     * @param string $group
     *
     * @return int
     */
    public function getGroupCount(string $group): int;

    /**
     * Return all elements number in all groups.
     *
     * @return int
     */
    public function getCount(): int;

    /**
     * Add extensions.
     *
     * @param array $extensions
     */
    public function addExtensions(array $extensions): void;

    /**
     * Get extensions.
     *
     * @return array
     */
    public function getExtensions(): array;
}

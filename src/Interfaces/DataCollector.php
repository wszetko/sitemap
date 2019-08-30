<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Interfaces;

use Wszetko\Sitemap\Items\Url;

/**
 * Interface DataCollector
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface DataCollector
{
    /**
     * DataCollector constructor.
     *
     * @param array|null $config
     */
    public function __construct(?array $config);

    /**
     * @param Url $item
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
     * @return array
     */
    public function fetchAll(): array;

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
    public function addExtension(array $extensions): void;

    /**
     * @return array
     */
    public function getExtensions(): array;
}

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

namespace Wszetko\Sitemap\Drivers\DataCollectors;

use Wszetko\Sitemap\Interfaces\DataCollector;
use Wszetko\Sitemap\Items\Url;

/**
 * Class Memory.
 *
 * @package Wszetko\Sitemap\Drivers\DataCollectors
 */
class Memory implements DataCollector
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @var array
     */
    private $element = [];

    /**
     * @var array
     */
    private $extensions = [];

    /**
     * DataCollector constructor.
     *
     * @param null|array $config
     */
    public function __construct(?array $config)
    {
    }

    /**
     * @param Url    $item
     * @param string $group
     */
    public function add(Url $item, string $group): void
    {
        if (!isset($this->items[$group])) {
            $this->items[$group] = [];
        }

        $this->addExtensions($item->getExtensions());
        $this->items[$group][] = $item->toArray();
    }

    /**
     * @param array $extensions
     */
    public function addExtensions(array $extensions): void
    {
        foreach ($extensions as $extension) {
            foreach ($extension as $ext) {
                $this->extensions[$ext::NAMESPACE_NAME] = $ext::NAMESPACE_URL;
            }
        }
    }

    /**
     * @param string $group
     *
     * @return array
     */
    public function fetch(string $group): ?array
    {
        if (!isset($this->items[$group])) {
            return null;
        }

        $element = $this->getGroupElement($group);

        if (($element + 1) > $this->getGroupCount($group)) {
            return null;
        }

        $result = $this->items[$group][$element];
        $this->incrementGroupElement($group);

        return $result;
    }

    /**
     * @param string $group
     *
     * @return int
     */
    public function getGroupCount(string $group): int
    {
        return count($this->items[$group]);
    }

    /**
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->items;
    }

    /**
     * @param string $group
     *
     * @return bool
     */
    public function isLast(string $group): bool
    {
        return (bool) !isset($this->items[$group][$this->getGroupElement($group) + 1]);
    }

    /**
     * @param string $group
     *
     * @return array
     */
    public function fetchGroup(string $group): array
    {
        return $this->items[$group];
    }

    /**
     * @return int
     */
    public function getGroupsCount(): int
    {
        return count($this->getGroups());
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return array_keys($this->items);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        $result = 0;

        foreach ($this->items as $item) {
            $result += count($item);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param string $group
     *
     * @return null|int
     */
    private function getGroupElement(string $group): ?int
    {
        if (!isset($this->element[$group])) {
            $this->element[$group] = 0;
        }

        return $this->element[$group];
    }

    /**
     * @param string $group
     */
    private function incrementGroupElement(string $group): void
    {
        if (isset($this->element[$group])) {
            ++$this->element[$group];
        }
    }
}

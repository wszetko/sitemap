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

use Wszetko\Sitemap\Items\Url;

/**
 * Class Memory.
 *
 * @package Wszetko\Sitemap\Drivers\DataCollectors
 */
class Memory extends AbstractDataCollector
{
    /**
     * Collection of added URLs.
     *
     * @var array
     */
    private $items = [];

    /**
     * Number of elements in each group.
     *
     * @var int[]
     */
    private $element = [];

    /**
     * Array of used extensions.
     *
     * @var array
     */
    private $extensions = [];

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function fetch(string $group): ?array
    {
        if (!isset($this->items[$group])) {
            return null;
        }

        $element = $this->getGroupElementsCount($group);

        if (($element + 1) > $this->getGroupCount($group)) {
            return null;
        }

        $result = $this->items[$group][$element];
        $this->incrementGroupElement($group);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getGroupCount(string $group): int
    {
        return count($this->items[$group]);
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(?string $group = null): ?array
    {
        if (is_string($group) && $group !== '') {
            if (isset($this->items[$group])) {
                return $this->items[$group];
            }

            return null;
        }

        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function isLast(string $group): bool
    {
        return !isset($this->items[$group][$this->getGroupElementsCount($group) + 1]);
    }

    /**
     * @inheritDoc
     */
    public function fetchGroup(string $group): array
    {
        return $this->items[$group];
    }

    /**
     * @inheritDoc
     */
    public function getGroupsCount(): int
    {
        return count($this->getGroups());
    }

    /**
     * @inheritDoc
     */
    public function getGroups(): array
    {
        return array_keys($this->items);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * Return number of elements in group.
     *
     * @param string $group
     *
     * @return int
     */
    private function getGroupElementsCount(string $group): int
    {
        if (!isset($this->element[$group])) {
            $this->element[$group] = 0;
        }

        return $this->element[$group];
    }

    /**
     * Increment number of elements in group.
     *
     * @param string $group
     */
    private function incrementGroupElement(string $group): void
    {
        if (isset($this->element[$group])) {
            ++$this->element[$group];
        }
    }
}

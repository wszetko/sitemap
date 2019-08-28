<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Interfaces;

/**
 * Interface XML
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface Item
{
    public function toArray(): array;
}

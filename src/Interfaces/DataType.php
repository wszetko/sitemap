<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Interfaces;

/**
 * Interface DataType
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface DataType
{
    /**
     * @param       $value
     * @param mixed ...$parameters
     *
     * @return \Wszetko\Sitemap\Interfaces\DataType
     */
    public function setValue($value, ...$parameters): self;

    /**
     * @return mixed
     */
    public function getValue();
}
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
 * Interface DataType.
 *
 * @package Wszetko\Sitemap\Interfaces
 */
interface DataType
{
    /**
     * Set value with optional parameters.
     *
     * @param mixed $value
     * @param array $parameters
     *
     * @return static
     */
    public function setValue($value, $parameters = []): DataType;

    /**
     * Return value of field in proper format.
     *
     * @return mixed
     */
    public function getValue();
}

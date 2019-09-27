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

namespace Wszetko\Sitemap\Items\DataTypes;

use Wszetko\Sitemap\Interfaces\DataType;
use Wszetko\Sitemap\Traits\DateTime;

/**
 * Class DateTimeType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class DateTimeType extends AbstractDataType
{
    use DateTime;

    /**
     * @param mixed $value
     * @param array $parameters
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public function setValue($value, $parameters = []): DataType
    {
        $value = $this->processDateTime($value, $this->isRequired());

        if (is_string($value)) {
            $this->value = $value;
        }

        return $this;
    }
}

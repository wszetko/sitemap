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

/**
 * Class YesNoType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class YesNoType extends AbstractDataType
{
    /**
     * @param       $value
     * @param mixed ...$parameters
     *
     * @return \Wszetko\Sitemap\Interfaces\DataType
     */
    public function setValue($value, ...$parameters): DataType
    {
        if ((is_string($value) && preg_grep("/{$value}/i", ['Yes', 'y', '1'])) || true === $value || 1 === $value) {
            $this->value = 'Yes';
        } elseif ((is_string($value) && preg_grep("/{$value}/i", ['No', 'n', '0'])) || false === $value || 0 === $value) {
            $this->value = 'No';
        }

        return $this;
    }
}

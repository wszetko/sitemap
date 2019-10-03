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

/**
 * Class IntegerType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class IntegerType extends FloatType
{
    /**
     * @inheritDoc
     */
    public function getValue()
    {
        $value = parent::getValue();

        return null === $value ? null : (string) round((float) $value);
    }
}

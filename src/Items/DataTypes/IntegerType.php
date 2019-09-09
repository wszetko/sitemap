<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

/**
 * Class IntegerType
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class IntegerType extends FloatType
{
    /**
     * @return mixed|string|null
     */
    public function getValue()
    {
        $value = parent::getValue();

        return is_null($value) ? null : strval(intval(round($value)));
    }
}
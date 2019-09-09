<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

use Wszetko\Sitemap\Interfaces\DataType;
use Wszetko\Sitemap\Traits\DateTime;

/**
 * Class DateTimeType
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class DateTimeType extends AbstractDataType
{
    use DateTime;

    /**
     * @param       $value
     * @param mixed ...$parameters
     *
     * @return \Wszetko\Sitemap\Interfaces\DataType
     */
    public function setValue($value, ...$parameters): DataType
    {
        if ($value = $this->processDateTime($value, $this->isRequired())) {
            $this->value = $value;
        }

        return $this;
    }
}
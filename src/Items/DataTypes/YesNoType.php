<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

use Wszetko\Sitemap\Interfaces\DataType;

/**
 * Class YesNoType
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
        if ((is_string($value) && preg_grep("/$value/i", ['Yes', 'y', '1'])) || $value == true) {
            $this->value = 'Yes';
        } elseif ((is_string($value) && preg_grep("/$value/i", ['No', 'n', '0'])) || $value == false) {
            $this->value = 'No';
        }

        return $this;
    }
}
